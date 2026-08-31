<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Support\Mark;

/**
 * NOTE: field names below are assumed to mirror MultipleChoiceQuestion's
 * schema pattern (Mark-cast `question`, Mark-cast `answer` as a model/sample
 * answer, `score`, `difficulty_level`, FKs, audit columns). Adjust
 * buildModelData() if EssayQuestion's actual columns differ — e.g. if it
 * uses `model_answer` instead of `answer`, or stores rubric/marking points
 * as a separate field.
 */
class EssayImportHandler implements QuestionTypeHandlerInterface
{
    public function existingHashes(AcademicTopic $topic): array
    {
        return \DB::table('essay_questions')
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
        return 'essay';
    }

    public function promptInstructions(): string
    {
        return <<<PROMPT
ESSAY / LONG-ANSWER QUESTIONS ("essay" array):
Extract questions that require a written, free-form answer (no lettered options) — e.g. "Describe...",
"Explain...", "List the steps...", "State the reason...". Do NOT include true/false or multiple-choice
items here.

For each item return:
- question_plain: the question text with HTML tags stripped
- question_html: the question text with original formatting preserved
- model_answer_plain: a brief model/sample answer or key marking points, with HTML tags stripped.
  If the document provides an explicit expected answer or marking scheme, use it (answer_source: "answer_key").
  If not, generate a concise, accurate model answer yourself from your own subject knowledge
  (answer_source: "inferred_knowledge"). Only leave this empty if you cannot produce anything reasonable
  (answer_source: "unknown").
- model_answer_html: the same model answer with basic formatting (e.g. <ul><li> for marking points) if useful,
  otherwise identical to model_answer_plain
- answer_source: "answer_key" | "inferred_knowledge" | "unknown"
- difficulty_level: "easy" | "medium" | "hard" (infer if not explicit, default "medium")
- score: number — use the marks shown in the document if present (e.g. "[15mk]" means score: 15), default 1 if not specified
PROMPT;
    }

    public function normalize(array $item): ?array
    {
        if (trim($item['question_plain'] ?? '') === '') {
            return null;
        }

        return $item;
    }

    public function buildModelData(array $item, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): ?array
    {
        $questionPlain = trim($item['question_plain'] ?? '');
        if ($questionPlain === '') {
            return null;
        }

        $answerPlain = trim($item['model_answer_plain'] ?? '');
        $answerHtml = $item['model_answer_html'] ?? $answerPlain;

        return [
            'question' => new Mark($questionPlain, $item['question_html'] ?? $questionPlain),
            'answer' => new Mark($answerPlain, $answerHtml),
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
        return EssayQuestion::create($modelData)->id;
    }

    public function warningFor(array $item, int $index): ?string
    {
        $source = $item['answer_source'] ?? 'unknown';
        $preview = mb_substr($item['question_plain'] ?? '', 0, 60);

        if ($source === 'inferred_knowledge') {
            return "Essay #{$index} (\"{$preview}...\"): no model answer was provided in the document — the AI generated one from its own subject knowledge. Please review before publishing.";
        }

        if ($source === 'unknown' || trim($item['model_answer_plain'] ?? '') === '') {
            return "Essay #{$index} (\"{$preview}...\"): no model answer could be generated. Please add one manually.";
        }

        return null;
    }
}
