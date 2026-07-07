<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Centralized AI document import service.
 *
 * Extracts a document (Word/PDF) ONCE, runs a SINGLE AI pass that classifies
 * and extracts multiple choice, true/false, and essay questions together
 * (most real documents — exam papers, question banks — mix all three in one
 * file), then hands each classified section off to its dedicated
 * QuestionTypeHandlerInterface implementation to validate, build, and save.
 *
 * To support a new question type: write a new handler implementing
 * QuestionTypeHandlerInterface and add it to the $handlers array below.
 * Nothing else in this class needs to change.
 */
class DocumentAiQuestionImportService extends AbstractAiQuestionImportService
{
    /** @var QuestionTypeHandlerInterface[] keyed by handler->key() */
    private array $handlers = [];

    public function __construct(
        \App\Services\ResearchAssistantService $chatService,
        MultipleChoiceImportHandler            $multipleChoice,
        TrueOrFalseImportHandler               $trueOrFalse,
        EssayImportHandler                     $essay,
    )
    {
        parent::__construct($chatService);

        foreach ([$multipleChoice, $trueOrFalse, $essay] as $handler) {
            $this->handlers[$handler->key()] = $handler;
        }
    }


    /**
     * @param QuestionTypeHandlerInterface[] $handlers
     */
    private function buildCombinedPrompt(array $extracted, AcademicTopic $topic, ?AcademicSubtopic $subtopic, array $handlers): string
    {
        $context = "Topic: {$topic->name}.";
        if ($subtopic) {
            $context .= " Subtopic: {$subtopic->name}.";
        }

        $formattingNote = $extracted['method'] === 'plain_no_formatting'
            ? 'NOTE: This source had no bold/underline formatting preserved (normal for PDFs). Use answer keys in the text where present, otherwise your own subject knowledge as a last resort — track this per item via answer_source as instructed below.'
            : 'Bold/underline formatting IS preserved below. Some documents bold entire question stems for visual styling only — that is not an answer signal. Only bold/underline specifically on an answer/option is meaningful.';

        $typeBlocks = implode("\n\n", array_map(fn($h) => $h->promptInstructions(), $handlers));
        $resultKeys = implode(', ', array_map(fn($h) => '"' . $h->key() . '"', $handlers));

        $exampleSections = [];
        foreach ($handlers as $h) {
            $exampleSections[] = "\"{$h->key()}\": []";
        }
        $exampleJson = "{\n  " . implode(",\n  ", $exampleSections) . "\n}";

        return <<<PROMPT
You are classifying and extracting questions from an academic document. The document may contain a MIX of
question types in the same file (e.g. an objectives section, a true/false section, and an essay section) —
classify each question into exactly ONE of the categories below based on its actual shape, not just its
section heading.

{$context}

{$formattingNote}

{$typeBlocks}

Return ONLY a single JSON object with these top-level keys: {$resultKeys}. Each key's value is an array of
the items described above for that type (empty array if none found). No markdown fences, no commentary.
Example shape:
{$exampleJson}

Document content:
{$extracted['html']}
PROMPT;
    }

    /**
     * @param QuestionTypeHandlerInterface[] $handlers
     */
    private function normalizeParsedResponse(array $parsed, array $handlers, string $extractionMethod): array
    {
        $results = [];
        $errors = [];

        foreach ($handlers as $key => $handler) {
            $rawItems = $parsed[$key] ?? [];
            if (!is_array($rawItems)) {
                $rawItems = [];
            }

            $normalized = [];

            foreach ($rawItems as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $clean = $handler->normalize($item);
                if ($clean === null) {
                    continue;
                }

                $index = count($normalized);
                $normalized[] = $clean;

                $warning = $handler->warningFor($clean, $index);
                if ($warning !== null) {
                    $errors[] = $warning;
                }
            }

            $results[$key] = $normalized;
        }

        return [
            'results' => $results,
            'errors' => $errors,
            'extraction_method' => $extractionMethod,
        ];
    }

    /**
     * Save already-extracted/edited results (e.g. after a user reviews a preview screen).
     *
     * @param array $results Shape: ['multiple_choice' => [...items], 'true_false' => [...], 'essay' => [...]]
     */
    public function save(array $results, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId, array $existingErrors = []): array
    {
        $createdIds = [];
        $errors = $existingErrors;
        $duplicateCount = 0;

        // Preload one hash-set per type — single query each, O(1) lookup during the loop.
        $dbHashes = [];
        foreach ($this->handlers as $key => $handler) {
            if (isset($results[$key]) && is_array($results[$key]) && count($results[$key]) > 0) {
                $dbHashes[$key] = $handler->existingHashes($topic);
            }
        }

        foreach ($results as $typeKey => $items) {
            $handler = $this->handlers[$typeKey] ?? null;
            if (!$handler) {
                continue;
            }

            $createdIds[$typeKey] = [];

            // Track hashes seen within this batch to catch duplicates inside
            // the same document (e.g. a question repeated on two pages).
            $batchHashes = [];

            foreach ($items as $index => $item) {
                try {
                    $hash = $handler->questionHash($item);

                    // Check against questions already in the database.
                    if (isset($dbHashes[$typeKey][$hash])) {
                        $preview = mb_substr(
                            strip_tags($item['question_plain'] ?? $item['statement_plain'] ?? ''),
                            0, 60
                        );
                        $errors[] = "{$typeKey} #{$index} (\"{$preview}…\"): skipped — an identical question already exists in this topic.";
                        $duplicateCount++;
                        continue;
                    }

                    // Check for duplicates within the current batch.
                    if (isset($batchHashes[$hash])) {
                        $preview = mb_substr(
                            strip_tags($item['question_plain'] ?? $item['statement_plain'] ?? ''),
                            0, 60
                        );
                        $errors[] = "{$typeKey} #{$index} (\"{$preview}…\"): skipped — duplicate of another question in this document.";
                        $duplicateCount++;
                        continue;
                    }

                    $modelData = $handler->buildModelData($item, $topic, $subtopic, $userId);

                    if ($modelData === null) {
                        $errors[] = "{$typeKey} #{$index}: missing required fields — skipped.";
                        continue;
                    }

                    $createdIds[$typeKey][] = $handler->create($modelData);

                    // Mark as seen so later items in this batch don't duplicate it.
                    $batchHashes[$hash] = true;
                    // Also add to the DB hash-set so a re-import in the same
                    // request (unlikely but safe) won't sneak duplicates through.
                    $dbHashes[$typeKey][$hash] = true;

                } catch (\Throwable $e) {
                    Log::error('Failed to save AI-imported question', [
                        'type' => $typeKey,
                        'error' => $e->getMessage(),
                        'item' => $item,
                    ]);
                    $errors[] = "{$typeKey} #{$index}: failed to save — {$e->getMessage()}";
                }
            }
        }

        $totalCreated = array_sum(array_map('count', $createdIds));

        $summary = [];
        if ($totalCreated > 0) {
            $summary[] = "{$totalCreated} question" . ($totalCreated === 1 ? '' : 's') . ' imported';
        }
        if ($duplicateCount > 0) {
            $summary[] = "{$duplicateCount} duplicate" . ($duplicateCount === 1 ? '' : 's') . ' skipped';
        }

        return [
            'created_ids' => $createdIds,
            'created_count' => $totalCreated,
            'duplicate_count' => $duplicateCount,
            'summary' => implode(', ', $summary),
            'errors' => $errors,
        ];
    }

    public function import(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null, ?int $userId = null, ?array $only = null): array
    {
        $preview = $this->preview($file, $topic, $subtopic, $only);
        return $this->save($preview['results'], $topic, $subtopic, $userId, $preview['errors']);
    }

    /**
     * Extract + classify questions from a file without saving them.
     * NOW SUPPORTS BATCH PROCESSING FOR LARGE DOCUMENTS.
     */
    public function preview(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null, ?array $only = null): array
    {
        $extracted = $this->extractWithFormatting($file);

        if (!$this->isMeaningfulContent($extracted['html'])) {
            return [
                'results' => [],
                'errors' => ['The document appears to be empty or has no extractable content.'],
                'extraction_method' => $extracted['method'],
            ];
        }

        $activeHandlers = $only !== null
            ? array_intersect_key($this->handlers, array_flip($only))
            : $this->handlers;

        if (empty($activeHandlers)) {
            throw new \InvalidArgumentException('No valid question type handlers selected.');
        }

        // --- BATCH PROCESSING: Split large documents into manageable chunks ---
        $chunks = $this->chunkContent($extracted['html']);
        $totalChunks = count($chunks);
        $allParsedResults = [];
        $chunkErrors = [];

        foreach ($chunks as $index => $chunk) {
            $chunkExtracted = [
                'html' => $chunk,
                'method' => $extracted['method'],
            ];

            $prompt = $this->buildCombinedPrompt($chunkExtracted, $topic, $subtopic, $activeHandlers);
            $aiResult = $this->callAi($prompt);

            if (!($aiResult['success'] ?? false)) {
                Log::error('Question AI extraction failed for chunk ' . ($index + 1), ['error' => $aiResult['error'] ?? 'unknown']);
                $chunkErrors[] = 'AI extraction failed for part ' . ($index + 1) . ' of ' . $totalChunks . ': ' . ($aiResult['error'] ?? 'unknown error');
                continue;
            }

            $parsed = $this->parseJsonResponse($aiResult['content'] ?? '');

            if ($parsed === null) {
                $chunkErrors[] = 'The AI did not return valid, parseable question data for part ' . ($index + 1) . ' of ' . $totalChunks . '.';
                continue;
            }

            // Merge parsed results from this chunk into the master array
            foreach ($activeHandlers as $key => $handler) {
                if (!isset($allParsedResults[$key])) {
                    $allParsedResults[$key] = [];
                }
                if (isset($parsed[$key]) && is_array($parsed[$key])) {
                    $allParsedResults[$key] = array_merge($allParsedResults[$key], $parsed[$key]);
                }
            }
        }

        // If all chunks failed, return early
        if (empty($allParsedResults) && !empty($chunkErrors)) {
            return [
                'results' => [],
                'errors' => $chunkErrors,
                'extraction_method' => $extracted['method'],
            ];
        }

        // Normalize the merged results
        $normalized = $this->normalizeParsedResponse($allParsedResults, $activeHandlers, $extracted['method']);
        
        // Prepend any chunk-level errors to the normalization errors
        $normalized['errors'] = array_merge($chunkErrors, $normalized['errors']);

        return $normalized;
    }

    /**
     * Split large HTML content into smaller chunks to avoid hitting AI context/output limits.
     * Attempts to break at natural boundaries (paragraphs, divs, double newlines) to avoid 
     * splitting a single question in half.
     */
    private function chunkContent(string $html, int $maxChunkSize = 15000): array
    {
        $html = trim($html);
        if (mb_strlen($html) <= $maxChunkSize) {
            return [$html];
        }

        $chunks = [];
        // Split by closing tags or double newlines to preserve structure
        $parts = preg_split('/(<\/(?:p|div|section|article|tr|table)>|<br\s*\/?>|\n{2,})/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $currentChunk = '';
        foreach ($parts as $part) {
            if (mb_strlen($currentChunk . $part) > $maxChunkSize) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = '';
                }
                
                // If the individual part is still larger than the limit, split it forcefully
                if (mb_strlen($part) > $maxChunkSize) {
                    $length = mb_strlen($part);
                    for ($i = 0; $i < $length; $i += $maxChunkSize) {
                        $chunks[] = mb_substr($part, $i, $maxChunkSize);
                    }
                } else {
                    $currentChunk = $part;
                }
            } else {
                $currentChunk .= $part;
            }
        }
        
        if (trim($currentChunk) !== '') {
            $chunks[] = trim($currentChunk);
        }
        
        return array_filter($chunks, fn($c) => trim($c) !== '');
    }
}
