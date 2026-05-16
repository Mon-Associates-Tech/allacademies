<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExamQuestion;
use App\MockExam\Models\MockExamSection;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueFalseQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MockExamQuestionService
{
    private const SOURCE_CLASS_MAP = [
        'multiple_choice' => MultipleChoiceQuestion::class,
        'true_false'      => TrueFalseQuestion::class,
        'essay'           => EssayQuestion::class,
    ];

    // ─── Public entry point ───────────────────────────────────────────────────

    /**
     * Pull questions from the source bank for a section and persist them.
     * Returns the count of questions actually created.
     */
    public function pullQuestionsForSection(
        MockExamSection $section,
        array $subtopicIds,
        array $topicIds,
        int $academicSubjectId
    ): int {
        $effectiveSubtopicIds = $this->resolveSubtopicIds($subtopicIds, $topicIds, $academicSubjectId);

        if (empty($effectiveSubtopicIds)) {
            Log::warning('MockExamQuestionService: no subtopics resolved', [
                'section_id'         => $section->id,
                'subtopic_ids'       => $subtopicIds,
                'topic_ids'          => $topicIds,
                'academic_subject_id'=> $academicSubjectId,
            ]);
            return 0;
        }

        // Build type → count map
        $typeCounts = $this->buildTypeCounts($section);

        $order   = 1;
        $created = 0;

        foreach ($typeCounts as $type => $count) {
            if ($count <= 0) {
                continue;
            }

            $sourceQuestions = $this->fetchSourceQuestions($type, $effectiveSubtopicIds, $count);

            foreach ($sourceQuestions as $source) {
                try {
                    $this->persistQuestion($section, $source, $type, $order++);
                    $created++;
                } catch (\Throwable $e) {
                    Log::error('MockExamQuestionService: failed to persist question', [
                        'source_type' => $type,
                        'source_id'   => $source->id,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }
        }

        return $created;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Walk up the academic hierarchy to collect subtopic IDs when the caller
     * has only provided topic or subject level selections.
     */
    private function resolveSubtopicIds(array $subtopicIds, array $topicIds, int $academicSubjectId): array
    {
        // Subtopics explicitly selected – use them directly
        if (! empty($subtopicIds)) {
            return $subtopicIds;
        }

        // Topics selected – get all their subtopics
        if (! empty($topicIds)) {
            return AcademicSubtopic::whereIn('academic_topic_id', $topicIds)
                ->pluck('id')
                ->toArray();
        }

        // Fall back to all subtopics under the subject
        $topicIdsForSubject = AcademicTopic::where('academic_subject_id', $academicSubjectId)
            ->pluck('id')
            ->toArray();

        return AcademicSubtopic::whereIn('academic_topic_id', $topicIdsForSubject)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Distribute section question_count across question types.
     *
     * @return array<string, int>
     */
    private function buildTypeCounts(MockExamSection $section): array
    {
        $total = (int) $section->question_count;

        if ($section->question_type !== 'mixed') {
            return [$section->question_type => $total];
        }

        // Distribute as evenly as possible across three types
        $base  = intdiv($total, 3);
        $extra = $total % 3; // 0, 1, or 2

        return [
            'multiple_choice' => $base + ($extra >= 1 ? 1 : 0),
            'true_false'      => $base + ($extra >= 2 ? 1 : 0),
            'essay'           => $base,
        ];
    }

    /**
     * Query the appropriate source model table and return random rows.
     */
    private function fetchSourceQuestions(string $type, array $subtopicIds, int $limit): Collection
    {
        $class = self::SOURCE_CLASS_MAP[$type] ?? null;

        if ($class === null) {
            return collect();
        }

        return $class::whereIn('academic_subtopic_id', $subtopicIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Snapshot a source question into mock_exam_questions.
     * Uses the HasQuestionAndAnswer trait methods on the source model to
     * extract clean text regardless of the Mark cast.
     */
    private function persistQuestion(
        MockExamSection $section,
        Model $source,
        string $type,
        int $order
    ): MockExamQuestion {
        // processQuestionModel is provided by the HasQuestionAndAnswer trait
        $processed = $source->processQuestionModel($source);

        $questionText = $processed['question'] ?? '';
        $answer       = $processed['answer']   ?? null;
        $options      = $processed['options']  ?? [];

        // Attempt to retrieve keywords stored directly on the source model
        $keywords = null;
        if (isset($source->keywords)) {
            $keywords = is_array($source->keywords)
                ? $source->keywords
                : (is_string($source->keywords) ? json_decode($source->keywords, true) : null);
        }

        return MockExamQuestion::create([
            'mock_exam_section_id' => $section->id,
            'source_type'          => $type,
            'source_id'            => $source->id,
            'question_text'        => $questionText,
            'options'              => ! empty($options) ? $options : null,
            'correct_answer'       => $type !== 'essay' ? $answer : null,
            'answer_explanation'   => $type === 'essay'  ? $answer : null,
            'answer_keywords'      => $keywords,
            'marks'                => (float) ($source->score ?? $section->marks_per_question),
            'order'                => $order,
            'difficulty_level'     => $source->difficulty_level ?? null,
        ]);
    }
}
