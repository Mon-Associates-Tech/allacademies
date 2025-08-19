<?php

namespace App\Livewire\Assessment;

use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Models\EssayQuestion;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RandomQuestionSelectionService implements QuestionSelectionInterface
{
    protected SubjectSelectionService $subjectSelectionService;

    public function __construct(SubjectSelectionService $subjectSelectionService)
    {
        $this->subjectSelectionService = $subjectSelectionService;
    }

    /**
     * Generate random questions based on selection criteria
     */
    public function generateQuestions(array $config): Collection
    {
        Log::info('generateQuestions called with config:', $config);

        if (!$this->validateConfiguration($config)) {
            Log::warning('Configuration validation failed');
            return collect();
        }

        // Debug: Check subject selection validation
        $subjectValidation = $this->subjectSelectionService->validateSelection(
            $config['subject_id'],
            $config['topic_id'] ?? null,
            $config['subtopic_id'] ?? null
        );

        Log::info('Subject validation result:', ['valid' => $subjectValidation]);

        if (!$subjectValidation) {
            Log::warning('Subject selection validation failed');
            return collect();
        }

        $questions = collect();
        $enabledTypes = array_filter($config['question_types'] ?? [], function($enabled) {
            return $enabled === true;
        });

        Log::info('Enabled question types:', $enabledTypes);

        if (empty($enabledTypes)) {
            Log::warning('No enabled question types');
            return collect();
        }

        // Distribute questions across enabled types
        $totalQuestions = $config['question_count'] ?? 10;
        $questionsPerType = intval($totalQuestions / count($enabledTypes));
        $remainder = $totalQuestions % count($enabledTypes);

        foreach ($enabledTypes as $type => $enabled) {
            $count = $questionsPerType;
            if ($remainder > 0) {
                $count++;
                $remainder--;
            }

            Log::info("Getting {$count} questions for type: {$type}");
            $typeQuestions = $this->getQuestionsByType($type, $config, $count);
            Log::info("Found {$typeQuestions->count()} questions for type: {$type}");
            $questions = $questions->merge($typeQuestions);
        }

        Log::info("Total questions generated: {$questions->count()}");
        return $questions->shuffle();
    }

    /**
     * Get available question counts for selection criteria
     */
    public function getAvailableQuestionCounts(array $config): array
    {
        Log::info('getAvailableQuestionCounts called with config:', $config);

        if (!$this->validateConfiguration($config)) {
            Log::warning('Configuration validation failed in getAvailableQuestionCounts');
            return [];
        }

        $counts = [];
        $questionTypes = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
        ];

        foreach ($questionTypes as $type => $class) {
            $query = $this->buildBaseQuery($class, $config);
            $count = $query->count();
            $counts[$type] = $count;
            Log::info("Question count for {$type}: {$count}");
        }

        $counts['total'] = array_sum($counts);
        Log::info('Total question counts:', $counts);
        return $counts;
    }

    /**
     * Validate question selection configuration
     */
    public function validateConfiguration(array $config): bool
    {
        Log::info('validateConfiguration called with:', $config);

        // Check required fields
        if (empty($config['subject_id'])) {
            Log::warning('Missing subject_id');
            return false;
        }

        if (empty($config['question_count']) || $config['question_count'] < 1) {
            Log::warning('Invalid question_count');
            return false;
        }

        // Check if at least one question type is enabled
        $questionTypes = $config['question_types'] ?? [];
        $hasEnabledType = false;
        foreach ($questionTypes as $enabled) {
            if ($enabled === true) {
                $hasEnabledType = true;
                break;
            }
        }

        if (!$hasEnabledType) {
            Log::warning('No question types enabled');
            return false;
        }

        Log::info('Configuration validation passed');
        return true;
    }

/**
 * Get questions for a specific question type
 */
public function getQuestionsByType(string $type, array $config, int $count): Collection
{
    Log::info("getQuestionsByType called for type: {$type}, count: {$count}");

    if ($count <= 0) {
        Log::warning("Invalid count: {$count}");
        return collect();
    }

    $class = $this->getQuestionClass($type);
    if (!$class) {
        Log::warning("Unknown question class for type: {$type}");
        return collect();
    }

    $query = $this->buildBaseQuery($class, $config);

    // Apply difficulty filter if specified
    if (!empty($config['difficulty']) && $config['difficulty'] !== 'all') {
        $query->where('difficulty_level', $config['difficulty']);
        Log::info("Applied difficulty filter: {$config['difficulty']}");
    }

    // Get available questions
    $availableQuestions = $query->get();
    Log::info("Available questions count: {$availableQuestions->count()}");

    if ($availableQuestions->isEmpty()) {
        Log::warning("No questions available for type: {$type}");
        return collect();
    }

    // Randomly select the required number of questions
    $selectedQuestions = $availableQuestions->count() <= $count
        ? $availableQuestions
        : $availableQuestions->random($count);

    Log::info("Selected {$selectedQuestions->count()} questions for type: {$type}");

    // Format questions for assessment
    return $selectedQuestions->map(function ($question) use ($type) {
        // Get the question data from the model
        $questionData = $question->getQuestion();

        return [
            'id' => $question->id,
            'type' => $type,
            'model' => $question,
            'formatted' => $questionData, // This ensures we get the proper question structure
            'difficulty' => $question->difficulty_level ?? 'medium',
            'points' => $question->score ?? 1,
            'subject_id' => $this->getSubjectIdFromQuestion($question),
            'topic_id' => $this->getTopicIdFromQuestion($question),
            'subtopic_id' => $question->academic_subtopic_id ?? null,
        ];
    });
}

    /**
     * Mix questions from different types according to configuration
     */
    public function mixQuestions(array $config): Collection
    {
        return $this->generateQuestions($config);
    }

    /**
     * Format questions for assessment display
     */
    public function formatQuestionsForAssessment(Collection $questions): Collection
    {
        return $questions->map(function ($question, $index) {
            $formatted = $question['formatted'] ?? [];

            // Ensure we're getting the correct data based on question type
            $baseData = [
                'index' => $index + 1,
                'id' => $question['id'],
                'type' => $question['type'],
                'question' => $formatted['question'] ?? '',
                'difficulty' => $question['difficulty'],
                'points' => $question['points'],
                'subject_id' => $question['subject_id'],
                'topic_id' => $question['topic_id'],
                'subtopic_id' => $question['subtopic_id'],
                'model' => $question['model'],
            ];

            // Add type-specific fields
            switch ($question['type']) {
                case 'multiple_choice_question':
                    $baseData['options'] = $formatted['options'] ?? [];
                    $baseData['answer'] = $formatted['answer'] ?? null;
                    break;

                case 'true_or_false_question':
                    $baseData['answer'] = $formatted['answer'] ?? null;
                    // No options needed for true/false
                    break;

                case 'essay_question':
                    // No options or specific answer for essay questions
                    break;
            }

            return $baseData;
        });
    }

    /**
     * Build base query for question selection
     */
    protected function buildBaseQuery(string $class, array $config)
    {
        Log::info("buildBaseQuery called for class: {$class}");
        Log::info("Config for buildBaseQuery:", $config);

        $query = $class::query();

        // Apply subject, topic, and subtopic filters
        if (!empty($config['subtopic_id'])) {
            // Filter by specific subtopic (keep this as it's direct)
            Log::info("Filtering by subtopic_id: {$config['subtopic_id']}");
            $query->where('academic_subtopic_id', $config['subtopic_id']);
        } elseif (!empty($config['topic_id'])) {
            // Filter by topic - only use direct topic relationship
            Log::info("Filtering by topic_id: {$config['topic_id']}");
            $query->where('academic_topic_id', $config['topic_id']);
        } elseif (!empty($config['subject_id'])) {
            // Filter by subject - only use direct topic relationship to subject
            Log::info("Filtering by subject_id: {$config['subject_id']}");
            $query->whereHas('academicTopic', function ($t) use ($config) {
                $t->where('academic_subject_id', $config['subject_id']);
            });
        }

        Log::info("Final query SQL: " . $query->toSql());
        Log::info("Final query bindings: " . json_encode($query->getBindings()));

        return $query;
    }

    /**
     * Get question class from type string
     */
    protected function getQuestionClass(string $type): ?string
    {
        $mapping = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
        ];

        return $mapping[$type] ?? null;
    }

    /**
     * Get subject ID from question model
     */
    protected function getSubjectIdFromQuestion($question): ?int
    {
        if ($question->subtopic && $question->subtopic->academicTopic) {
            return $question->subtopic->academicTopic->academic_subject_id;
        }

        return null;
    }

    /**
     * Get topic ID from question model
     */
    protected function getTopicIdFromQuestion($question): ?int
    {
        if ($question->subtopic) {
            return $question->subtopic->academic_topic_id;
        }

        return null;
    }

    /**
     * Get distribution of questions by difficulty
     */
    public function getQuestionDistribution(array $config): array
    {
        if (!$this->validateConfiguration($config)) {
            return [];
        }

        $distribution = [];
        $questionTypes = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
        ];

        foreach ($questionTypes as $type => $class) {
            $baseQuery = $this->buildBaseQuery($class, $config);

            $distribution[$type] = [
                'easy' => (clone $baseQuery)->where('difficulty_level', 'easy')->count(),
                'medium' => (clone $baseQuery)->where('difficulty_level', 'medium')->count(),
                'hard' => (clone $baseQuery)->where('difficulty_level', 'hard')->count(),
            ];

            $distribution[$type]['total'] = array_sum($distribution[$type]);
        }

        return $distribution;
    }

    /**
     * Generate balanced questions (equal distribution across difficulty levels)
     */
    public function generateBalancedQuestions(array $config): Collection
    {
        if (!$this->validateConfiguration($config)) {
            return collect();
        }

        $questions = collect();
        $enabledTypes = array_filter($config['question_types'] ?? [], function($enabled) {
            return $enabled === true;
        });

        if (empty($enabledTypes)) {
            return collect();
        }

        $totalQuestions = $config['question_count'] ?? 10;
        $questionsPerType = intval($totalQuestions / count($enabledTypes));
        $remainder = $totalQuestions % count($enabledTypes);

        foreach ($enabledTypes as $type => $enabled) {
            $count = $questionsPerType;
            if ($remainder > 0) {
                $count++;
                $remainder--;
            }

            // Get balanced questions for this type
            $typeQuestions = $this->getBalancedQuestionsByType($type, $config, $count);
            $questions = $questions->merge($typeQuestions);
        }

        return $questions->shuffle();
    }

    /**
     * Get balanced questions by type (distributed across difficulty levels)
     */
    protected function getBalancedQuestionsByType(string $type, array $config, int $count): Collection
    {
        if ($count <= 0) {
            return collect();
        }

        $class = $this->getQuestionClass($type);
        if (!$class) {
            return collect();
        }

        $difficulties = ['easy', 'medium', 'hard'];
        $questionsPerDifficulty = intval($count / 3);
        $remainder = $count % 3;

        $questions = collect();

        foreach ($difficulties as $difficulty) {
            $difficultyCount = $questionsPerDifficulty;
            if ($remainder > 0) {
                $difficultyCount++;
                $remainder--;
            }

            if ($difficultyCount > 0) {
                $query = $this->buildBaseQuery($class, $config);
                $query->where('difficulty_level', $difficulty);

                $availableQuestions = $query->get();

                if ($availableQuestions->isNotEmpty()) {
                    $selectedQuestions = $availableQuestions->count() <= $difficultyCount
                        ? $availableQuestions
                        : $availableQuestions->random($difficultyCount);

                    $formattedQuestions = $selectedQuestions->map(function ($question) use ($type) {
                        return [
                            'id' => $question->id,
                            'type' => $type,
                            'model' => $question,
                            'formatted' => $question->getQuestion(),
                            'difficulty' => $question->difficulty_level ?? 'medium',
                            'points' => $question->score ?? 1,
                            'subject_id' => $this->getSubjectIdFromQuestion($question),
                            'topic_id' => $this->getTopicIdFromQuestion($question),
                            'subtopic_id' => $question->academic_subtopic_id ?? null,
                        ];
                    });

                    $questions = $questions->merge($formattedQuestions);
                }
            }
        }

        return $questions;
    }

    /**
     * Debug method to check what's in the database
     */
    public function debugQuestionData(array $config): array
    {
        $debug = [];

        // Check if subject exists
        $subject = AcademicSubject::find($config['subject_id']);
        $debug['subject'] = $subject ? $subject->toArray() : null;

        // Check topics for subject
        $topics = AcademicTopic::where('academic_subject_id', $config['subject_id'])->get();
        $debug['topics'] = $topics->toArray();

        // Check subtopics for subject
        $subtopics = AcademicSubtopic::whereHas('academicTopic', function ($query) use ($config) {
            $query->where('academic_subject_id', $config['subject_id']);
        })->get();
        $debug['subtopics'] = $subtopics->toArray();

        // Check questions for each type
        $questionTypes = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
        ];

        foreach ($questionTypes as $type => $class) {
            $debug['questions'][$type] = $class::take(5)->get()->toArray();
        }

        return $debug;
    }
}
