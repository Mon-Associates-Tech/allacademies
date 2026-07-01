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
        MultipleChoiceImportHandler $multipleChoice,
        TrueOrFalseImportHandler $trueOrFalse,
        EssayImportHandler $essay,
    ) {
        parent::__construct($chatService);

        foreach ([$multipleChoice, $trueOrFalse, $essay] as $handler) {
            $this->handlers[$handler->key()] = $handler;
        }
    }

    /**
     * Extract + classify questions from a file without saving them.
     *
     * @param  string[]|null  $only  Restrict to specific type keys, e.g. ['multiple_choice'].
     *                               Null (default) extracts all registered types at once.
     */
    public function preview(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null, ?array $only = null): array
    {
        $extracted = $this->extractWithFormatting($file);

        if (! $this->isMeaningfulContent($extracted['html'])) {
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

        $prompt = $this->buildCombinedPrompt($extracted, $topic, $subtopic, $activeHandlers);
        $aiResult = $this->callAi($prompt);

        if (! ($aiResult['success'] ?? false)) {
            Log::error('Question AI extraction failed', ['error' => $aiResult['error'] ?? 'unknown']);

            return [
                'results' => [],
                'errors' => ['AI extraction failed: '.($aiResult['error'] ?? 'unknown error')],
                'extraction_method' => $extracted['method'],
            ];
        }

        $parsed = $this->parseJsonResponse($aiResult['content'] ?? '');

        if ($parsed === null) {
            return [
                'results' => [],
                'errors' => ['The AI did not return valid, parseable question data.'],
                'extraction_method' => $extracted['method'],
            ];
        }

        return $this->normalizeParsedResponse($parsed, $activeHandlers, $extracted['method']);
    }

    /**
     * Extract + save in one call.
     */
    public function import(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null, ?int $userId = null, ?array $only = null): array
    {
        $preview = $this->preview($file, $topic, $subtopic, $only);

        return $this->save($preview['results'], $topic, $subtopic, $userId, $preview['errors']);
    }

    /**
     * Save already-extracted/edited results (e.g. after a user reviews a preview screen).
     *
     * @param  array  $results  Shape: ['multiple_choice' => [...items], 'true_false' => [...], 'essay' => [...]]
     */
    public function save(array $results, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId, array $existingErrors = []): array
    {
        $createdIds = [];
        $errors = $existingErrors;

        foreach ($results as $typeKey => $items) {
            $handler = $this->handlers[$typeKey] ?? null;
            if (! $handler) {
                continue;
            }

            $createdIds[$typeKey] = [];

            foreach ($items as $index => $item) {
                try {
                    $modelData = $handler->buildModelData($item, $topic, $subtopic, $userId);

                    if ($modelData === null) {
                        $errors[] = "{$typeKey} #{$index}: missing required fields — skipped.";

                        continue;
                    }

                    $createdIds[$typeKey][] = $handler->create($modelData);
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

        return [
            'created_ids' => $createdIds,
            'created_count' => $totalCreated,
            'errors' => $errors,
        ];
    }

    /**
     * @param  QuestionTypeHandlerInterface[]  $handlers
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

        $typeBlocks = implode("\n\n", array_map(fn ($h) => $h->promptInstructions(), $handlers));
        $resultKeys = implode(', ', array_map(fn ($h) => '"'.$h->key().'"', $handlers));

        $exampleSections = [];
        foreach ($handlers as $h) {
            $exampleSections[] = "\"{$h->key()}\": []";
        }
        $exampleJson = "{\n  ".implode(",\n  ", $exampleSections)."\n}";

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
     * @param  QuestionTypeHandlerInterface[]  $handlers
     */
    private function normalizeParsedResponse(array $parsed, array $handlers, string $extractionMethod): array
    {
        $results = [];
        $errors = [];

        foreach ($handlers as $key => $handler) {
            $rawItems = $parsed[$key] ?? [];
            if (! is_array($rawItems)) {
                $rawItems = [];
            }

            $normalized = [];

            foreach ($rawItems as $item) {
                if (! is_array($item)) {
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
}
