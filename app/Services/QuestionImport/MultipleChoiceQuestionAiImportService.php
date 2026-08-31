<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use App\Support\Mark;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Dedicated AI-powered import pipeline for Multiple Choice Questions only.
 *
 * Responsibilities:
 *  - Extract content from PDF/Word while preserving bold/underline formatting
 *    wherever the source format allows it (this is what signals the correct answer).
 *  - Ask the AI model to extract ONLY multiple choice questions, returning both
 *    a plain-text and an HTML-formatted version of the question + each option,
 *    plus the correct option letter.
 *  - Cast question/options into App\Support\Mark (up = plain, down = html), exactly
 *    like the manual TinyMCE form does.
 *  - Persist as MultipleChoiceQuestion rows (or return a preview payload first).
 */
class MultipleChoiceQuestionAiImportService
{
    public function __construct(
        private readonly ResearchAssistantService $chatService,
    ) {}

    /**
     * Preview MCQs extracted from a file without saving them.
     */
    public function preview(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null): array
    {
        $extracted = $this->extractWithFormatting($file);

        if (trim(strip_tags($extracted['html'])) === '' || strlen(trim(strip_tags($extracted['html']))) < 10) {
            return [
                'questions' => [],
                'errors' => ['The document appears to be empty or has no extractable content.'],
                'extraction_method' => $extracted['method'],
            ];
        }

        return $this->extractQuestions($extracted, $topic, $subtopic);
    }

    /**
     * Import (persist) MCQs straight from a file.
     */
    public function import(UploadedFile $file, AcademicTopic $topic, ?AcademicSubtopic $subtopic = null, ?int $userId = null): array
    {
        $preview = $this->preview($file, $topic, $subtopic);

        return $this->save($preview['questions'], $topic, $subtopic, $userId, $preview['errors']);
    }

    /**
     * Persist questions that were already extracted/previewed (e.g. after user
     * reviewed and edited the preview screen).
     */
    public function save(array $questions, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId, array $existingErrors = []): array
    {
        $created = [];
        $errors = $existingErrors;

        foreach ($questions as $index => $q) {
            try {
                $row = $this->buildModelData($q, $topic, $subtopic, $userId);

                if ($row === null) {
                    $errors[] = "Question #{$index}: missing question text or options A/B — skipped.";
                    continue;
                }

                $created[] = MultipleChoiceQuestion::create($row)->id;
            } catch (\Throwable $e) {
                Log::error('Failed to save AI-imported MCQ', [
                    'error' => $e->getMessage(),
                    'question' => $q,
                ]);
                $errors[] = "Question #{$index}: failed to save — {$e->getMessage()}";
            }
        }

        return [
            'created_ids' => $created,
            'created_count' => count($created),
            'errors' => $errors,
        ];
    }

    /**
     * Build the MultipleChoiceQuestion::create() payload from one AI-extracted item.
     */
    private function buildModelData(array $q, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): ?array
    {
        $questionPlain = trim($q['question_plain'] ?? '');
        $questionHtml = $q['question_html'] ?? $questionPlain;

        $optA = trim($q['options'][0]['plain'] ?? '');
        $optB = trim($q['options'][1]['plain'] ?? '');

        if ($questionPlain === '' || $optA === '' || $optB === '') {
            return null;
        }

        $answerLetter = strtoupper(trim($q['correct_option'] ?? ''));
        if (! in_array($answerLetter, ['A', 'B', 'C', 'D', 'E'], true)) {
            $answerLetter = '';
        }

        $option = fn (int $i) => new Mark(
            trim($q['options'][$i]['plain'] ?? ''),
            $q['options'][$i]['html'] ?? trim($q['options'][$i]['plain'] ?? '')
        );

        return [
            'question' => new Mark($questionPlain, $questionHtml),
            'option_a' => $option(0),
            'option_b' => $option(1),
            'option_c' => $option(2),
            'option_d' => $option(3),
            'option_e' => $option(4),
            'answer' => $answerLetter,
            'score' => $q['score'] ?? 1,
            'difficulty_level' => $q['difficulty_level'] ?? 'medium',
            'academic_topic_id' => $topic->id,
            'academic_subtopic_id' => $subtopic?->id,
            'added_by' => $userId,
            'modified_by' => $userId,
        ];
    }

    private function buildPrompt(array $extracted, AcademicTopic $topic, ?AcademicSubtopic $subtopic): string
    {
        $context = "Topic: {$topic->name}.";
        if ($subtopic) {
            $context .= " Subtopic: {$subtopic->name}.";
        }

        $formattingNote = $extracted['method'] === 'plain_no_formatting'
            ? "NOTE: This source had no bold/underline formatting preserved (this is normal for PDFs). First, look for an explicit answer cue in the text itself: an answer key section (e.g. 'Answers: 1-B, 2-A'), 'Correct: B', or a marker next to an option. If you find one, set answer_source to \"answer_key\". If there is NO such cue anywhere in the document (common for blank exam papers / question banks), you may use your own subject-matter knowledge to determine the most likely correct answer — in that case set answer_source to \"inferred_knowledge\". Only set correct_option to an empty string and answer_source to \"unknown\" if you genuinely cannot determine an answer either way."
            : "The correct answer for each question is normally the option wrapped in <strong>/<b> bold tags (or <u> underline tags). IMPORTANT: some documents also bold the question stem itself for visual styling — that stem bolding is NOT an answer signal, ignore it completely. Only look for bold/underline formatting on text that appears specifically inside the option list (after the A)/B)/C)/D)/E) markers) to identify correct_option. Set answer_source to \"formatting\" when you used this signal. If none of the options show distinct bold/underline formatting (i.e. either all options are styled the same way, or none are), fall back to looking for an explicit answer key in the text (answer_source: \"answer_key\"), and only after that use your own subject knowledge (answer_source: \"inferred_knowledge\"), or \"unknown\" if you cannot determine it at all.";

        return <<<PROMPT
You are extracting MULTIPLE CHOICE QUESTIONS ONLY from an academic document. Ignore any essay, short-answer, or fill-in-the-blank questions entirely.

CRITICAL — EXCLUDE TRUE/FALSE QUESTIONS: if a question's options are (or reduce to) exactly "True" and "False" — in any order, casing, or phrasing like "T"/"F" — it is a True/False question, NOT a multiple choice question. Do not include it in the output at all, even if it is formatted identically to the objective questions in the document (lettered options, same section, etc). Only include questions whose options are substantive answer choices (not literally true/false).

{$context}

{$formattingNote}

For every multiple choice question found, return:
- question_plain: the question text with all HTML tags stripped
- question_html: the question text with original formatting preserved (bold/italic/underline tags only, no other markup)
- options: an array of objects, one per option present (2 to 5 options), each with:
    - "letter": "A" | "B" | "C" | "D" | "E" (in source order)
    - "plain": option text with HTML tags stripped
    - "html": option text with original formatting preserved
- correct_option: the single letter (A-E) of the correct option, based on the rule above
- answer_source: "formatting" | "answer_key" | "inferred_knowledge" | "unknown" — how you determined correct_option
- difficulty_level: "easy" | "medium" | "hard" (infer if not explicit, default "medium")
- score: a number (default 1 if not specified)

Return ONLY a JSON object in this exact shape, no markdown fences, no commentary:
{
  "questions": [
    {
      "question_plain": "...",
      "question_html": "...",
      "options": [
        {"letter": "A", "plain": "...", "html": "..."},
        {"letter": "B", "plain": "...", "html": "..."}
      ],
      "correct_option": "B",
      "answer_source": "formatting",
      "difficulty_level": "medium",
      "score": 1
    }
  ]
}

Document content:
{$extracted['html']}
PROMPT;
    }

    private function parseJsonResponse(string $response): ?array
    {
        $response = trim($response);

        if (str_starts_with($response, '```')) {
            $response = preg_replace('/^```(?:json)?\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
        }

        try {
            $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // Fall back to extracting the first {...} block in case the model
            // added stray text around the JSON.
            if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $response, $m)) {
                try {
                    $data = json_decode($m[0], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e2) {
                    Log::warning('MCQ AI response JSON parse failed', ['preview' => substr($response, 0, 300)]);

                    return null;
                }
            } else {
                Log::warning('MCQ AI response had no JSON', ['preview' => substr($response, 0, 300)]);

                return null;
            }
        }

        // Normalize options into index-keyed [0..4] so buildModelData() can rely on order A-E.
        if (isset($data['questions']) && is_array($data['questions'])) {
            $filtered = [];

            foreach ($data['questions'] as $q) {
                $ordered = [];
                foreach ($q['options'] ?? [] as $opt) {
                    $letter = strtoupper($opt['letter'] ?? '');
                    $idx = match ($letter) {
                        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4,
                        default => count($ordered),
                    };
                    $ordered[$idx] = ['plain' => $opt['plain'] ?? '', 'html' => $opt['html'] ?? ($opt['plain'] ?? '')];
                }
                ksort($ordered);
                $q['options'] = array_values($ordered);

                // Defensive guard: drop anything that is actually a True/False question
                // even if the AI missed the instruction to exclude it.
                if ($this->isTrueFalseQuestion($q['options'])) {
                    continue;
                }

                $filtered[] = $q;
            }

            $data['questions'] = $filtered;
        }

        return $data;
    }

    /**
     * Detect True/False-shaped questions so they never slip into the MCQ pipeline,
     * regardless of how the AI labelled them.
     */
    private function isTrueFalseQuestion(array $options): bool
    {
        if (count($options) !== 2) {
            return false;
        }

        $normalized = array_map(
            fn ($opt) => strtolower(trim($opt['plain'] ?? '')),
            $options
        );
        sort($normalized);

        $trueFalseSets = [
            ['false', 'true'],
            ['f', 't'],
        ];

        return in_array($normalized, $trueFalseSets, true);
    }

    /**
     * Extract HTML content from the uploaded file, preserving bold/underline
     * formatting where the underlying format supports it.
     *
     * Returns: ['html' => string, 'method' => 'docx_html'|'pdf_html'|'plain_no_formatting']
     */
    private function extractWithFormatting(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'docx', 'doc' => $this->extractWordHtml($file),
            'pdf' => $this->extractPdfHtml($file),
            default => throw new \InvalidArgumentException("Unsupported file format: {$extension}. Only PDF and Word documents are supported."),
        };
    }

    private function extractWordHtml(UploadedFile $file): array
    {
        $phpWord = WordIOFactory::load($file->getRealPath());
        $html = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $html .= $this->wordElementToHtml($element);
            }
        }

        return ['html' => trim($html), 'method' => 'docx_html'];
    }

    private function wordElementToHtml($element): string
    {
        if (method_exists($element, 'getText')) {
            $text = htmlspecialchars($element->getText());
            $style = method_exists($element, 'getFontStyle') ? $element->getFontStyle() : null;

            if ($style) {
                if (method_exists($style, 'isBold') && $style->isBold()) {
                    $text = "<strong>{$text}</strong>";
                }
                if (method_exists($style, 'isItalic') && $style->isItalic()) {
                    $text = "<em>{$text}</em>";
                }
                if (method_exists($style, 'getUnderline') && $style->getUnderline() && $style->getUnderline() !== 'none') {
                    $text = "<u>{$text}</u>";
                }
            }

            return $text."\n";
        }

        if (method_exists($element, 'getElements')) {
            $html = '';
            foreach ($element->getElements() as $child) {
                $html .= $this->wordElementToHtml($child);
            }

            return $html."\n";
        }

        return '';
    }

    /**
     * PDFs lose bold formatting through pdftotext / Smalot text extraction.
     * Prefer `pdftohtml` (poppler-utils — same package as pdftotext) which
     * preserves <b>/<i> runs. Fall back to plain text if it's unavailable.
     */
    private function extractPdfHtml(UploadedFile $file): array
    {
        if ($this->isPdfToHtmlAvailable()) {
            $html = $this->extractPdfViaPdfToHtml($file->getRealPath());
            if ($html !== null && trim(strip_tags($html)) !== '') {
                return ['html' => $html, 'method' => 'pdf_html'];
            }
        }

        // Fallback: plain text only, no bold signal available.
        $parser = new PdfParser;
        $pdf = $parser->parseFile($file->getRealPath());
        $text = $pdf->getText();
        $text = preg_replace('/\n{3,}/', "\n\n", trim($text));

        return ['html' => htmlspecialchars($text), 'method' => 'plain_no_formatting'];
    }

    private function isPdfToHtmlAvailable(): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $output = [];
        $returnCode = 0;
        @exec('which pdftohtml 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }

    private function extractPdfViaPdfToHtml(string $filePath): ?string
    {
        $outputBase = tempnam(sys_get_temp_dir(), 'pdf_html_');
        @unlink($outputBase); // pdftohtml creates $outputBase.html itself
        $outputFile = $outputBase.'.html';

        // -i: ignore images, -s: single output html file, -noframes: no frameset wrapper
        $command = 'pdftohtml -i -s -noframes '.escapeshellarg($filePath).' '.escapeshellarg($outputBase).' 2>&1';
        exec($command, $cmdOutput, $returnCode);

        if ($returnCode !== 0 || ! file_exists($outputFile)) {
            Log::warning('pdftohtml extraction failed, falling back to plain text', [
                'output' => implode("\n", $cmdOutput),
            ]);
            @unlink($outputFile);

            return null;
        }

        $raw = file_get_contents($outputFile);
        @unlink($outputFile);

        // Strip everything down to the body content; keep only tags relevant to us.
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $raw, $m)) {
            $raw = $m[1];
        }

        // Keep <b>, <i>, <u>, normalize <b> variants pdftohtml emits.
        $allowedTags = '<b><strong><i><em><u><br><p><div>';
        $clean = strip_tags($raw, $allowedTags);
        $clean = preg_replace('/<(div|p)[^>]*>/i', "\n", $clean);
        $clean = preg_replace('/<\/(div|p)>/i', '', $clean);
        $clean = preg_replace('/\n{3,}/', "\n\n", trim($clean));

        return $clean;
    }

    /**
     * Ask the AI to extract MCQs from the formatted content and parse the JSON response.
     * NOW SUPPORTS BATCH PROCESSING FOR LARGE DOCUMENTS.
     */
    private function extractQuestions(array $extracted, AcademicTopic $topic, ?AcademicSubtopic $subtopic): array
    {
        $chunks = $this->chunkContent($extracted['html']);
        $totalChunks = count($chunks);
        $allQuestions = [];
        $errors = [];

        foreach ($chunks as $index => $chunk) {
            $chunkExtracted = [
                'html' => $chunk,
                'method' => $extracted['method'],
            ];

            $prompt = $this->buildPrompt($chunkExtracted, $topic, $subtopic);
            $result = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.2,
                'response_length' => 400000,
            ]);

            if (!($result['success'] ?? false)) {
                Log::error('MCQ AI extraction failed for chunk ' . ($index + 1), ['error' => $result['error'] ?? 'unknown']);
                $errors[] = 'AI extraction failed for part ' . ($index + 1) . ' of ' . $totalChunks . ': ' . ($result['error'] ?? 'unknown error');
                continue;
            }

            $parsed = $this->parseJsonResponse($result['content'] ?? '');
            if ($parsed === null) {
                $errors[] = 'The AI did not return valid, parseable question data for part ' . ($index + 1) . ' of ' . $totalChunks . '.';
                continue;
            }

            $questions = $parsed['questions'] ?? [];
            $allQuestions = array_merge($allQuestions, $questions);
        }

        // Process warnings for all merged questions
        foreach ($allQuestions as $i => $q) {
            $source = $q['answer_source'] ?? 'unknown';
            $preview = mb_substr($q['question_plain'] ?? '', 0, 60);
            if ($source === 'inferred_knowledge') {
                $errors[] = "Question #{$i} (\"{$preview}...\"): no answer cue found in the document — the AI guessed \"{$q['correct_option']}\" from its own subject knowledge. Please verify before publishing.";
            } elseif ($source === 'unknown' || empty($q['correct_option'])) {
                $errors[] = "Question #{$i} (\"{$preview}...\"): could not determine a correct answer. Please set it manually.";
            }
        }

        return [
            'questions' => $allQuestions,
            'errors' => $errors,
            'extraction_method' => $extracted['method'],
        ];
    }

    // ... (buildPrompt, parseJsonResponse, isTrueFalseQuestion, extractWithFormatting, etc. remain the same)

    /**
     * Split large HTML content into smaller chunks to avoid hitting AI context/output limits.
     */
    private function chunkContent(string $html, int $maxChunkSize = 15000): array
    {
        $html = trim($html);
        if (mb_strlen($html) <= $maxChunkSize) {
            return [$html];
        }

        $chunks = [];
        $parts = preg_split('/(<\/(?:p|div|section|article|tr|table)>|<br\s*\/?>|\n{2,})/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $currentChunk = '';
        foreach ($parts as $part) {
            if (mb_strlen($currentChunk . $part) > $maxChunkSize) {
                if ($currentChunk !== '') {
                    $chunks[] = trim($currentChunk);
                    $currentChunk = '';
                }
                
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
