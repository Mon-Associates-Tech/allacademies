<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\MultipleChoiceQuestion;
use App\Support\Mark;

class MultipleChoiceImportHandler implements QuestionTypeHandlerInterface
{
    public function existingHashes(AcademicTopic $topic): array
    {
        return \DB::table('multiple_choice_questions')
            ->where('academic_topic_id', $topic->id)
            ->whereNull('deleted_at')
            ->pluck('question')
            ->map(fn ($json) => $this->hashText(
                json_decode($json, true)['down'] ?? ''
            ))
            ->mapWithKeys(fn ($hash) => [$hash => true])
            ->all();
    }

    public function questionHash(array $item): string
    {
        return $this->hashText($item['question_plain'] ?? '');
    }

    private function hashText(string $text): string
    {
        return md5(mb_strtolower(trim(strip_tags($text))));
    }

    public function key(): string
    {
        return 'multiple_choice';
    }

    public function promptInstructions(): string
    {
        return <<<PROMPT
MULTIPLE CHOICE QUESTIONS ("multiple_choice" array):
Extract questions that have 2-5 substantive lettered options where exactly one is correct.

CRITICAL — DO NOT put True/False questions here: if a question's options are (or reduce to) exactly
"True" and "False" — in any order, casing, or phrasing like "T"/"F" — it belongs in the "true_false"
array instead, NOT here, even if it is formatted identically to other objective questions in the document
(lettered options, same section, etc).

OPTION TEXT CLEANING — this is important: documents commonly prefix each option with its letter marker,
e.g. "A) London", "(A) London", "A. London", "A: London". Strip that prefix completely from the option
text before returning it. The "letter" field already captures the letter; the "plain" and "html" fields
must contain ONLY the substantive answer text. So "A) London" → letter:"A", plain:"London", html:"London".
Apply this stripping even if the prefix appears inside an HTML tag, e.g. "<strong>B) Paris</strong>"
→ letter:"B", plain:"Paris", html:"<strong>Paris</strong>".

For each item return:
- question_plain: question text with all HTML tags stripped
- question_html: question text with original formatting preserved (bold/italic/underline only)
- options: array of 2-5 objects, each {"letter": "A".."E", "plain": "...", "html": "..."}, in source order.
  The "plain" and "html" values must NOT include the letter prefix (A), B., A: etc) — strip it.
- correct_option: the letter (A-E) of the correct option
- answer_source: "formatting" | "answer_key" | "inferred_knowledge" | "unknown" — how you determined correct_option.
  Bold/underline ON A SPECIFIC OPTION = "formatting". An explicit answer key elsewhere in the document = "answer_key".
  No cue at all, so you used your own subject knowledge = "inferred_knowledge". Could not determine = "unknown"
  (leave correct_option empty in that case).
  NOTE: documents sometimes bold the entire question stem for visual styling — that is NOT an answer signal,
  ignore stem bolding and only look for bold/underline specifically inside the option list.
- difficulty_level: "easy" | "medium" | "hard" (infer if not explicit, default "medium")
- score: number (default 1 if not specified)
PROMPT;
    }

    public function normalize(array $item): ?array
    {
        $ordered = [];
        foreach ($item['options'] ?? [] as $opt) {
            $letter = strtoupper($opt['letter'] ?? '');
            $idx = match ($letter) {
                'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4,
                default => count($ordered),
            };
            $ordered[$idx] = [
                'plain' => $this->stripOptionPrefix($opt['plain'] ?? ''),
                'html'  => $this->stripOptionPrefixFromHtml($opt['html'] ?? ($opt['plain'] ?? '')),
            ];
        }
        ksort($ordered);
        $item['options'] = array_values($ordered);

        if ($this->isTrueFalseShaped($item['options'])) {
            return null;
        }

        if (trim($item['question_plain'] ?? '') === '' || count($item['options']) < 2) {
            return null;
        }

        return $item;
    }

    public function buildModelData(array $item, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): ?array
    {
        $optA = trim($item['options'][0]['plain'] ?? '');
        $optB = trim($item['options'][1]['plain'] ?? '');

        if ($optA === '' || $optB === '') {
            return null;
        }

        $answerLetter = strtoupper(trim($item['correct_option'] ?? ''));
        if (! in_array($answerLetter, ['A', 'B', 'C', 'D', 'E'], true)) {
            $answerLetter = '';
        }

        $option = fn (int $i) => new Mark(
            trim($item['options'][$i]['plain'] ?? ''),
            $item['options'][$i]['html'] ?? trim($item['options'][$i]['plain'] ?? '')
        );

        return [
            'question' => new Mark(trim($item['question_plain'] ?? ''), $item['question_html'] ?? $item['question_plain'] ?? ''),
            'option_a' => $option(0),
            'option_b' => $option(1),
            'option_c' => $option(2),
            'option_d' => $option(3),
            'option_e' => $option(4),
            'answer' => $answerLetter,
            'score' => $item['score'] ?? 1,
            'difficulty_level' => $item['difficulty_level'] ?? 'medium',
            'academic_topic_id' => $topic->id,
            'academic_subtopic_id' => $subtopic?->id,
            'added_by' => $userId,
            'modified_by' => $userId,
        ];
    }

    public function create(array $modelData): int
    {
        return MultipleChoiceQuestion::create($modelData)->id;
    }

    public function warningFor(array $item, int $index): ?string
    {
        $source = $item['answer_source'] ?? 'unknown';
        $preview = mb_substr($item['question_plain'] ?? '', 0, 60);

        if ($source === 'inferred_knowledge') {
            return "MCQ #{$index} (\"{$preview}...\"): no answer cue found in the document — the AI guessed \"{$item['correct_option']}\" from its own subject knowledge. Please verify before publishing.";
        }

        if ($source === 'unknown' || empty($item['correct_option'])) {
            return "MCQ #{$index} (\"{$preview}...\"): could not determine a correct answer. Please set it manually.";
        }

        return null;
    }

    /**
     * Strip leading option-letter prefixes from plain text.
     * Handles: "A) text", "(A) text", "A. text", "A: text", "A text" (single letter + space).
     * Works for A–E in both upper and lower case.
     */
    private function stripOptionPrefix(string $text): string
    {
        $stripped = preg_replace(
            '/^\s*\(?[A-Ea-e]\)?[\.\)\:\-]\s*/u',
            '',
            $text
        );

        return trim($stripped ?? $text);
    }

    /**
     * Strip leading option-letter prefixes from HTML text.
     * Handles prefixes that appear before any opening tag, or at the very start
     * of the text content inside the first tag (e.g. "<strong>A) Paris</strong>").
     */
    private function stripOptionPrefixFromHtml(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        // Case 1: prefix appears before any HTML tag — strip it directly.
        // e.g. "A) <strong>Paris</strong>"
        $stripped = preg_replace(
            '/^\s*\(?[A-Ea-e]\)?[\.\)\:\-]\s*/u',
            '',
            $html
        );

        if ($stripped !== $html) {
            return trim($stripped ?? $html);
        }

        // Case 2: prefix is inside the first opening tag's text content.
        // e.g. "<strong>A) Paris</strong>" → "<strong>Paris</strong>"
        $replaced = preg_replace_callback(
            '/^(<[^>]+>)\s*\(?[A-Ea-e]\)?[\.\)\:\-]\s*/u',
            fn ($m) => $m[1],
            $html
        );

        return trim($replaced ?? $html);
    }

    private function isTrueFalseShaped(array $options): bool
    {
        if (count($options) !== 2) {
            return false;
        }

        $normalized = array_map(fn ($opt) => strtolower(trim($opt['plain'] ?? '')), $options);
        sort($normalized);

        return in_array($normalized, [['false', 'true'], ['f', 't']], true);
    }
}
