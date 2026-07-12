<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamQuestionPersistenceService
{
    public function persistQuestionsForExam(GeneralExam $exam, array $sectionsWithQuestions): void
    {
        DB::transaction(function () use ($exam, $sectionsWithQuestions) {
            foreach ($exam->sections as $index => $section) {
                $questions = $sectionsWithQuestions[$index] ?? [];

                if (empty($questions)) {
                    continue;
                }

                $this->persistQuestionsForSection($section, $questions);
            }

            $exam->recalculateTotalMarks();
        });
    }

    private function persistQuestionsForSection(GeneralExamSection $section, array $questions): void
    {
        $existingQuestionIds = $section->questions->pluck('id')->toArray();
        $incomingQuestionIds = [];

        foreach ($questions as $order => $questionData) {
            if (! is_array($questionData)) {
                continue;
            }

            if (isset($questionData['placeholder']) && $questionData['placeholder']) {
                continue;
            }

            if (isset($questionData['hardened']) && $questionData['hardened']) {
                continue;
            }

            $attributes = [
                'general_exam_id'         => $section->general_exam_id,
                'type'                    => $this->normalizeQuestionType($questionData['type'] ?? 'multiple_choice'),
                'question'                => $questionData['question'] ?? '',
                'explanation'             => $questionData['explanation'] ?? null,
                'options'                 => $this->formatOptions($questionData),
                'correct_answer'          => $questionData['correct_answer'] ?? $questionData['answer'] ?? null,
                'grading_rubric'          => $questionData['grading_rubric'] ?? null,
                'keywords'                => $questionData['keywords'] ?? [],
                'marks'                   => $questionData['marks'] ?? $questionData['score'] ?? 1,
                'difficulty'              => $questionData['difficulty'] ?? $questionData['difficulty_level'] ?? 'medium',
                'order'                   => $order + 1,
                'ai_generated'            => $questionData['ai_generated'] ?? false,
                'is_edited'               => $questionData['is_edited'] ?? false,
                // ── Source traceability ──────────────────────────────────────
                // Populated by ExamQuestionPreviewService when pulling questions
                // from the question bank.  Null for AI-generated questions.
                'source_question_id'      => isset($questionData['source_question_id'])
                                                ? (int) $questionData['source_question_id']
                                                : null,
            ];

            if (! empty($questionData['id'])) {
                $questionId = (int) $questionData['id'];
                $incomingQuestionIds[] = $questionId;

                $section->questions()->where('id', $questionId)->update($attributes);
                Log::debug('Updated existing question', ['question_id' => $questionId, 'section_id' => $section->id]);
            } else {
                $newQuestion = $section->questions()->create($attributes);
                $incomingQuestionIds[] = $newQuestion->id;
                Log::debug('Created new question', ['question_id' => $newQuestion->id, 'section_id' => $section->id]);
            }
        }

        $questionsToDelete = array_diff($existingQuestionIds, $incomingQuestionIds);
        if (! empty($questionsToDelete)) {
            $section->questions()->whereIn('id', $questionsToDelete)->delete();
            Log::info('Deleted orphaned questions', [
                'section_id'    => $section->id,
                'deleted_count' => count($questionsToDelete),
                'deleted_ids'   => $questionsToDelete,
            ]);
        }

        $section->recalculateTotalMarks();
    }

    private function normalizeQuestionType(string $type): string
    {
        return match ($type) {
            'true_false', 'true/false' => 'true_false',
            'multiple_choice', 'mcq'   => 'multiple_choice',
            'short_answer'             => 'short_answer',
            'essay'                    => 'essay',
            default                    => 'multiple_choice',
        };
    }

    private function formatOptions(array $questionData): ?array
    {
        $type = $questionData['type'] ?? 'multiple_choice';

        if ($type === 'true_false') {
            return [
                ['key' => 'True',  'value' => 'True'],
                ['key' => 'False', 'value' => 'False'],
            ];
        }

        if ($type === 'multiple_choice' && ! empty($questionData['options'])) {
            $options   = array_values(array_filter($questionData['options']));
            $formatted = [];
            foreach ($options as $index => $option) {
                $formatted[] = [
                    'key'   => chr(65 + $index),
                    'value' => $option,
                ];
            }

            return $formatted;
        }

        return null;
    }
}
