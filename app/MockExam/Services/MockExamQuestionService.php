<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExamQuestion;
use App\MockExam\Models\MockExamSection;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MockExamQuestionService
{
    private const SOURCE_CLASS_MAP = [
        'multiple_choice' => MultipleChoiceQuestion::class,
        'true_false'      => TrueOrFalseQuestion::class,
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
        $resolved = $this->resolveTopicAndSubtopicIds($subtopicIds, $topicIds, $academicSubjectId);

        if (empty($resolved['subtopic_ids']) && empty($resolved['topic_ids'])) {
            Log::warning('MockExamQuestionService: no topics or subtopics resolved', [
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

            $sourceQuestions = $this->fetchSourceQuestions(
                $type,
                $resolved['subtopic_ids'],
                $resolved['topic_ids'],
                $count
            );

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
     * Resolve topic and subtopic IDs from the academic hierarchy.
     * Returns both topic_ids and subtopic_ids for flexible querying.
     */
    private function resolveTopicAndSubtopicIds(array $subtopicIds, array $topicIds, int $academicSubjectId): array
    {
        Log::info('MockExamQuestionService: resolving topics and subtopics', [
            'input_subtopic_ids' => $subtopicIds,
            'input_topic_ids' => $topicIds,
            'academic_subject_id' => $academicSubjectId,
        ]);

        $resolvedTopicIds = [];
        $resolvedSubtopicIds = [];

        // If subtopics explicitly selected
        if (!empty($subtopicIds)) {
            $resolvedSubtopicIds = $subtopicIds;
            // Also get their parent topic IDs
            $resolvedTopicIds = AcademicSubtopic::whereIn('id', $subtopicIds)
                ->pluck('academic_topic_id')
                ->unique()
                ->toArray();
        }
        // If topics selected
        elseif (!empty($topicIds)) {
            $resolvedTopicIds = $topicIds;
            // Get all subtopics under these topics
            $resolvedSubtopicIds = AcademicSubtopic::whereIn('academic_topic_id', $topicIds)
                ->pluck('id')
                ->toArray();
        }
        // Fall back to all topics/subtopics under the subject
        else {
            $resolvedTopicIds = AcademicTopic::where('academic_subject_id', $academicSubjectId)
                ->pluck('id')
                ->toArray();
            $resolvedSubtopicIds = AcademicSubtopic::whereIn('academic_topic_id', $resolvedTopicIds)
                ->pluck('id')
                ->toArray();
        }

        Log::info('MockExamQuestionService: resolved IDs', [
            'topic_count' => count($resolvedTopicIds),
            'subtopic_count' => count($resolvedSubtopicIds),
        ]);

        return [
            'topic_ids' => $resolvedTopicIds,
            'subtopic_ids' => $resolvedSubtopicIds,
        ];
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
     * Queries both academic_subtopic_id and academic_topic_id for maximum coverage.
     */
    private function fetchSourceQuestions(string $type, array $subtopicIds, array $topicIds, int $limit): Collection
    {
        $class = self::SOURCE_CLASS_MAP[$type] ?? null;

        if ($class === null) {
            Log::warning('MockExamQuestionService: unknown question type', ['type' => $type]);
            return collect();
        }

        Log::info('MockExamQuestionService: fetching questions', [
            'type' => $type,
            'class' => $class,
            'subtopic_ids' => $subtopicIds,
            'topic_ids' => $topicIds,
            'limit' => $limit,
        ]);

        // Query questions that match either subtopic_id OR topic_id
        $query = $class::query();

        if (!empty($subtopicIds) || !empty($topicIds)) {
            $query->where(function ($q) use ($subtopicIds, $topicIds) {
                if (!empty($subtopicIds)) {
                    $q->whereIn('academic_subtopic_id', $subtopicIds);
                }
                if (!empty($topicIds)) {
                    $q->orWhereIn('academic_topic_id', $topicIds);
                }
            });
        }

        $questions = $query->inRandomOrder()->limit($limit)->get();

        Log::info('MockExamQuestionService: fetched questions', [
            'type' => $type,
            'count' => $questions->count(),
        ]);

        return $questions;
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
