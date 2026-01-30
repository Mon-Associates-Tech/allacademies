<?php

namespace App\Services;

use App\Models\AcademicSubtopic;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;

class QuestionAvailabilityChecker
{
    /**
     * Check if there are enough questions available for the given configuration
     *
     * @param  array  $questionConfig  The question configuration from the assignment
     * @param  int  $subjectId  The subject ID
     * @param  array  $selectedTopics  The selected topic IDs
     * @param  array  $selectedSubtopics  The selected subtopic IDs
     * @return array Contains 'valid' boolean and 'message' string
     */
    public function checkQuestionAvailability(array $questionConfig, int $subjectId, array $selectedTopics = [], array $selectedSubtopics = []): array
    {
        $totalRequestedQuestions = 0;
        $questionTypes = [];

        // Process each question type configuration
        foreach ($questionConfig as $config) {
            if (! empty($config['type']) && ! empty($config['count']) && $config['count'] > 0) {
                $totalRequestedQuestions += $config['count'];
                $questionTypes[] = [
                    'type' => $config['type'],
                    'count' => $config['count'],
                    'difficulty' => $config['difficulty'] ?? 'all',
                ];
            }
        }

        if ($totalRequestedQuestions <= 0) {
            return ['valid' => true, 'message' => 'No questions requested'];
        }

        // Count available questions based on selection
        $availableQuestions = 0;
        $details = [];

        if (! empty($selectedSubtopics)) {
            // Count questions in selected subtopics
            foreach ($selectedSubtopics as $subtopicId) {
                $subtopicQuestions = $this->getQuestionCountForSubtopic($subtopicId, $questionTypes);
                $availableQuestions += $subtopicQuestions['total'];
                $details["subtopic_$subtopicId"] = $subtopicQuestions;
            }
        } elseif (! empty($selectedTopics)) {
            // Count questions in selected topics
            foreach ($selectedTopics as $topicId) {
                $topicQuestions = $this->getQuestionCountForTopic($topicId, $questionTypes);
                $availableQuestions += $topicQuestions['total'];
                $details["topic_$topicId"] = $topicQuestions;
            }
        } else {
            // Count questions in the entire subject
            $subjectQuestions = $this->getQuestionCountForSubject($subjectId, $questionTypes);
            $availableQuestions = $subjectQuestions['total'];
            $details["subject_$subjectId"] = $subjectQuestions;
        }

        if ($availableQuestions < $totalRequestedQuestions) {
            return [
                'valid' => false,
                'message' => "Not enough questions available. Requested: $totalRequestedQuestions, Available: $availableQuestions",
                'details' => $details,
            ];
        }

        return ['valid' => true, 'message' => "Sufficient questions available: $availableQuestions", 'details' => $details];
    }

    /**
     * Get question count for a specific subtopic
     */
    private function getQuestionCountForSubtopic(int $subtopicId, array $questionTypes): array
    {
        $counts = [];
        $total = 0;

        foreach ($questionTypes as $typeConfig) {
            $type = $typeConfig['type'];
            $difficulty = $typeConfig['difficulty'];

            $model = $this->getQuestionModel($type);
            if ($model) {
                $query = $model::where('academic_subtopic_id', $subtopicId);
                if ($difficulty !== 'all') {
                    $query->where('difficulty_level', $difficulty);
                }
                $count = $query->count();
                $counts[$type] = $count;
                $total += $count;
            }
        }

        $counts['total'] = $total;

        return $counts;
    }

    /**
     * Get question count for a specific topic
     */
    private function getQuestionCountForTopic(int $topicId, array $questionTypes): array
    {
        $counts = [];
        $total = 0;

        $subtopicIds = AcademicSubtopic::where('academic_topic_id', $topicId)->pluck('id')->toArray();

        if (! empty($subtopicIds)) {
            foreach ($questionTypes as $typeConfig) {
                $type = $typeConfig['type'];
                $difficulty = $typeConfig['difficulty'];

                $model = $this->getQuestionModel($type);
                if ($model) {
                    $query = $model::whereIn('academic_subtopic_id', $subtopicIds);
                    if ($difficulty !== 'all') {
                        $query->where('difficulty_level', $difficulty);
                    }
                    $count = $query->count();
                    $counts[$type] = $count;
                    $total += $count;
                }
            }
        }

        $counts['total'] = $total;

        return $counts;
    }

    /**
     * Get question count for a specific subject
     */
    private function getQuestionCountForSubject(int $subjectId, array $questionTypes): array
    {
        $counts = [];
        $total = 0;

        $subtopicIds = AcademicSubtopic::whereHas('academicTopic', function ($query) use ($subjectId) {
            $query->where('academic_subject_id', $subjectId);
        })->pluck('id')->toArray();

        if (! empty($subtopicIds)) {
            foreach ($questionTypes as $typeConfig) {
                $type = $typeConfig['type'];
                $difficulty = $typeConfig['difficulty'];

                $model = $this->getQuestionModel($type);
                if ($model) {
                    $query = $model::whereIn('academic_subtopic_id', $subtopicIds);
                    if ($difficulty !== 'all') {
                        $query->where('difficulty_level', $difficulty);
                    }
                    $count = $query->count();
                    $counts[$type] = $count;
                    $total += $count;
                }
            }
        }

        $counts['total'] = $total;

        return $counts;
    }

    /**
     * Get question model class by type
     */
    private function getQuestionModel(string $type): ?string
    {
        switch ($type) {
            case 'multiple_choice_question':
                return MultipleChoiceQuestion::class;
            case 'true_or_false_question':
                return TrueOrFalseQuestion::class;
            case 'essay_question':
                return EssayQuestion::class;
            default:
                return null;
        }
    }
}
