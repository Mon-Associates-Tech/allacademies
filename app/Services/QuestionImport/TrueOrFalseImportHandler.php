<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;

/**
 * NOTE: field names below (`question`, `answer`, `score`, `difficulty_level`,
 * `academic_topic_id`, `academic_subtopic_id`, `added_by`, `modified_by`) are
 * assumed to mirror MultipleChoiceQuestion's schema/fillable, since both use
 * the same HasQuestionAndAnswer trait pattern. Adjust buildModelData() if
 * TrueOrFalseQuestion's actual columns differ (e.g. if `answer` is stored as
 * a string "true"/"false" rather than a boolean).
 */
class TrueOrFalseImportHandler implements QuestionTypeHandlerInterface
{
    public function key(): string
    {
        return 'true_false';
    }

    public function promptInstructions(): string
    {
        return <<<PROMPT
TRUE/FALSE QUESTIONS ("true_false" array):
Extract questions whose options are exactly "True" and "False" (in any order, casing, or phrasing like "T"/"F").
Do NOT include any multiple-choice question with substantive answer options here, even if it sits in the same
section as true/false items.

For each item return:
- statement_plain: the statement/question text with HTML tags stripped
- statement_html: the statement/question text with original formatting preserved
- correct_answer: boolean true or false
- answer_source: "formatting" | "answer_key" | "inferred_knowledge" | "unknown" — how you determined correct_answer
  (bold/underline on "True" or "False" = "formatting"; an explicit answer key elsewhere = "answer_key"; no cue at
  all so you used subject knowledge = "inferred_knowledge"; could not determine = "unknown")
- difficulty_level: "easy" | "medium" | "hard" (infer if not explicit, default "medium")
- score: number (default 1 if not specified)
PROMPT;
    }

    public function normalize(array $item): ?array
    {
        if (trim($item['statement_plain'] ?? '') === '') {
            return null;
        }

        return $item;
    }

    public function buildModelData(array $item, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): ?array
    {
        $statementPlain = trim($item['statement_plain'] ?? '');
        if ($statementPlain === '') {
            return null;
        }

        $answer = $item['correct_answer'] ?? null;
        if (! is_bool($answer)) {
            // Tolerate the AI returning a string instead of a JSON boolean.
            $normalized = strtolower(trim((string) $answer));
            $answer = match ($normalized) {
                'true', 't', '1' => true,
                'false', 'f', '0' => false,
                default => null,
            };
        }

        return [
            'question' => new Mark($statementPlain, $item['statement_html'] ?? $statementPlain),
            'answer' => $answer, // null if undetermined — caller should treat as "needs manual review"
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
        return TrueOrFalseQuestion::create($modelData)->id;
    }

    public function warningFor(array $item, int $index): ?string
    {
        $source = $item['answer_source'] ?? 'unknown';
        $preview = mb_substr($item['statement_plain'] ?? '', 0, 60);

        if ($source === 'inferred_knowledge') {
            return "True/False #{$index} (\"{$preview}...\"): no answer cue found in the document — the AI guessed from its own subject knowledge. Please verify before publishing.";
        }

        if ($source === 'unknown' || ! array_key_exists('correct_answer', $item) || ! is_bool($item['correct_answer'] ?? null)) {
            return "True/False #{$index} (\"{$preview}...\"): could not determine the correct answer. Please set it manually.";
        }

        return null;
    }
}
