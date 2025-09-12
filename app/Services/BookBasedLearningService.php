<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\ReadingProgress;
use App\Models\QuizSession;
use App\Models\ReadingAchievement;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class BookBasedLearningService
{
    protected $chatService;

    public function __construct(AcademicChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Get personalized book recommendations for a user
     */
    public function getPersonalizedRecommendations(User $user, int $limit = 10): Collection
    {
        // Get user's reading history and preferences
        $readingHistory = $this->getUserReadingHistory($user);
        $favoriteGenres = $this->extractFavoriteGenres($readingHistory);
        $averageRating = $this->getAverageUserRating($user);
        $currentLevel = $this->determineUserReadingLevel($user);

        // Build recommendation query
        $query = Book::where('is_active', true)
            ->whereJsonContains('academic_levels', $user->academic_level)
            ->where('difficulty_score', '>=', max(1, $currentLevel - 1))
            ->where('difficulty_score', '<=', min(10, $currentLevel + 1));

        // Filter by age appropriateness
        if ($user->age) {
            $query->where('recommended_age_min', '<=', $user->age)
                ->where('recommended_age_max', '>=', $user->age);
        }

        // Boost books in favorite genres
        if (!empty($favoriteGenres)) {
            $query->whereIn('genre', $favoriteGenres);
        }

        // Exclude already read books
        $readBookIds = $user->readingProgress()->pluck('book_id')->toArray();
        if (!empty($readBookIds)) {
            $query->whereNotIn('id', $readBookIds);
        }

        return $query->with(['chapters'])
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($book) use ($user) {
                return $this->enrichBookWithRecommendationData($book, $user);
            });
    }

    /**
     * Generate adaptive quiz based on user's performance
     */
    public function generateAdaptiveQuiz(User $user, Book $book, array $parameters): array
    {
        // Analyze user's previous performance with this book
        $previousQuizzes = QuizSession::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'completed')
            ->get();

        // Determine adaptive difficulty
        $adaptiveDifficulty = $this->calculateAdaptiveDifficulty($previousQuizzes, $parameters['difficulty'] ?? 'medium');

        // Identify weak areas from previous quizzes
        $weakAreas = $this->identifyWeakAreas($previousQuizzes);

        // Focus on chapters/sections where user struggled
        $focusAreas = $this->identifyFocusAreas($user, $book, $weakAreas);

        // Build context with adaptive parameters
        $adaptiveContext = array_merge($parameters, [
            'adaptive_difficulty' => $adaptiveDifficulty,
            'focus_areas' => $focusAreas,
            'weak_concepts' => $weakAreas,
            'user_performance_history' => $this->getUserPerformanceHistory($user, $book)
        ]);

        return $this->generateQuizWithContext($book, $adaptiveContext, $user);
    }

    /**
     * Analyze reading comprehension and provide insights
     */
    public function analyzeReadingComprehension(User $user, Book $book, array $quizResults): array
    {
        $analysis = [
            'overall_comprehension' => $this->calculateOverallComprehension($quizResults),
            'strength_areas' => [],
            'improvement_areas' => [],
            'reading_level_assessment' => '',
            'personalized_recommendations' => []
        ];

        // Analyze by question types
        $questionTypePerformance = $this->analyzeQuestionTypePerformance($quizResults);

        foreach ($questionTypePerformance as $type => $performance) {
            if ($performance['accuracy'] >= 0.8) {
                $analysis['strength_areas'][] = [
                    'area' => $this->getReadableQuestionType($type),
                    'accuracy' => $performance['accuracy'],
                    'description' => $this->getStrengthDescription($type, $performance['accuracy'])
                ];
            } elseif ($performance['accuracy'] < 0.6) {
                $analysis['improvement_areas'][] = [
                    'area' => $this->getReadableQuestionType($type),
                    'accuracy' => $performance['accuracy'],
                    'recommendations' => $this->getImprovementRecommendations($type),
                    'practice_activities' => $this->getPracticeActivities($type, $user->learning_style)
                ];
            }
        }

        // Assess reading level
        $analysis['reading_level_assessment'] = $this->assessReadingLevel($user, $book, $quizResults);

        // Generate personalized recommendations
        $analysis['personalized_recommendations'] = $this->generateComprehensionRecommendations($user, $book, $analysis);

        return $analysis;
    }

    /**
     * Track and update reading achievements
     */
    public function checkAndAwardAchievements(User $user, string $activityType, array $activityData): array
    {
        $newAchievements = [];

        switch ($activityType) {
            case 'quiz_completed':
                $newAchievements = array_merge(
                    $newAchievements,
                    $this->checkQuizAchievements($user, $activityData)
                );
                break;

            case 'book_completed':
                $newAchievements = array_merge(
                    $newAchievements,
                    $this->checkReadingAchievements($user, $activityData)
                );
                break;

            case 'reading_session':
                $newAchievements = array_merge(
                    $newAchievements,
                    $this->checkEngagementAchievements($user, $activityData)
                );
                break;
        }

        // Award new achievements
        foreach ($newAchievements as $achievement) {
            ReadingAchievement::award(
                $user->id,
                $achievement['type'],
                $achievement['name'],
                $achievement['description'],
                $achievement['criteria']
            );
        }

        return $newAchievements;
    }

    /**
     * Generate discussion questions for book clubs or group discussions
     */
    public function generateDiscussionQuestions(Book $book, array $parameters): array
    {
        $context = [
            'book_title' => $book->title,
            'author' => $book->author,
            'genre' => $book->genre,
            'themes' => $book->themes ?? [],
            'academic_level' => $parameters['academic_level'] ?? 'high_school',
            'discussion_focus' => $parameters['focus'] ?? 'general',
            'group_size' => $parameters['group_size'] ?? 'small',
            'time_available' => $parameters['time_minutes'] ?? 45
        ];

        $prompt = $this->buildDiscussionQuestionsPrompt($context);

        $chatParameters = [
            'message' => $prompt,
            'academic_level' => $parameters['academic_level'] ?? 'high_school',
            'subject' => 'language_arts',
            'topics' => ['discussion', 'literary_analysis', 'critical_thinking'],
            'response_format' => 'structured',
            'creativity_level' => 0.7,
            'response_length' => 1200
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            return $this->parseDiscussionQuestions($result['content'], $context);
        }

        // Fallback questions
        return $this->getFallbackDiscussionQuestions($book, $context);
    }

    /**
     * Create a reading plan for a book or series
     */
    public function createReadingPlan(User $user, Book $book, array $parameters): array
    {
        $totalPages = $book->total_pages;
        $targetDays = $parameters['target_days'] ?? 30;
        $dailyReadingTime = $parameters['daily_minutes'] ?? 30; // minutes
        $userReadingSpeed = $this->estimateUserReadingSpeed($user, $book);

        // Calculate daily page target
        $dailyPageTarget = ceil($totalPages / $targetDays);

        // Adjust based on user's available time and reading speed
        $pagesPerSession = floor(($dailyReadingTime * $userReadingSpeed) / 60);

        if ($pagesPerSession < $dailyPageTarget) {
            // Need more time or adjust target
            $recommendedDays = ceil($totalPages / $pagesPerSession);
            $alternativePlan = true;
        } else {
            $recommendedDays = $targetDays;
            $alternativePlan = false;
        }

        // Create chapter-based milestones
        $milestones = $this->createReadingMilestones($book, $recommendedDays);

        // Generate personalized reading strategies
        $strategies = $this->generateReadingStrategies($user, $book);

        return [
            'book' => [
                'title' => $book->title,
                'author' => $book->author,
                'total_pages' => $totalPages,
                'estimated_reading_time' => $book->estimated_reading_time
            ],
            'plan' => [
                'target_days' => $recommendedDays,
                'daily_page_target' => ceil($totalPages / $recommendedDays),
                'daily_time_needed' => ceil((ceil($totalPages / $recommendedDays) * 60) / $userReadingSpeed),
                'alternative_suggested' => $alternativePlan
            ],
            'milestones' => $milestones,
            'reading_strategies' => $strategies,
            'weekly_goals' => $this->generateWeeklyGoals($milestones),
            'comprehension_checkpoints' => $this->generateComprehensionCheckpoints($book)
        ];
    }

    /**
     * Provide contextual vocabulary support
     */
    public function getVocabularySupport(Book $book, array $parameters): array
    {
        $chapterId = $parameters['chapter_id'] ?? null;
        $pageNumber = $parameters['page_number'] ?? null;
        $difficulty = $parameters['difficulty'] ?? 'medium';
        $userLevel = $parameters['user_level'] ?? 'intermediate';

        // Get chapter context if specified
        $chapter = null;
        if ($chapterId) {
            $chapter = BookChapter::find($chapterId);
        }

        // Build vocabulary request
        $vocabPrompt = $this->buildVocabularyPrompt($book, $chapter, $parameters);

        $chatParameters = [
            'message' => $vocabPrompt,
            'academic_level' => $userLevel,
            'subject' => 'language_arts',
            'topics' => ['vocabulary', 'reading_comprehension'],
            'response_format' => 'structured',
            'creativity_level' => 0.4,
            'response_length' => 1000
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            return $this->parseVocabularySupport($result['content'], $book, $chapter);
        }

        // Fallback vocabulary support
        return $this->getFallbackVocabularySupport($book, $chapter, $userLevel);
    }

    // ==========================================
    // PROTECTED HELPER METHODS
    // ==========================================

    /**
     * Get user's reading history with performance data
     */
    protected function getUserReadingHistory(User $user): Collection
    {
        return $user->readingProgress()
            ->with(['book', 'book.reviews' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Extract favorite genres from reading history
     */
    protected function extractFavoriteGenres(Collection $readingHistory): array
    {
        $genreCounts = [];

        foreach ($readingHistory as $progress) {
            if ($progress->book && $progress->book->genre) {
                $genre = $progress->book->genre;
                $genreCounts[$genre] = ($genreCounts[$genre] ?? 0) + 1;
            }
        }

        arsort($genreCounts);
        return array_slice(array_keys($genreCounts), 0, 3);
    }

    /**
     * Get average user rating for completed books
     */
    protected function getAverageUserRating(User $user): float
    {
        return $user->bookReviews()->avg('rating') ?? 3.0;
    }

    /**
     * Determine user's current reading level
     */
    protected function determineUserReadingLevel(User $user): int
    {
        $quizzes = $user->quizSessions()
            ->where('status', 'completed')
            ->get();

        if ($quizzes->isEmpty()) {
            return 5; // Default medium difficulty
        }

        $averageScore = $quizzes->avg(function($quiz) {
            return $quiz->results['percentage'] ?? 0;
        });

        // Convert percentage to difficulty scale (1-10)
        if ($averageScore >= 90) return 8; // Advanced
        if ($averageScore >= 80) return 6; // Intermediate-Advanced
        if ($averageScore >= 70) return 5; // Intermediate
        if ($averageScore >= 60) return 4; // Beginner-Intermediate
        return 3; // Beginner
    }

    /**
     * Enrich book data with recommendation information
     */
    protected function enrichBookWithRecommendationData(Book $book, User $user): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'genre' => $book->genre,
            'description' => $book->description,
            'total_pages' => $book->total_pages,
            'estimated_reading_time' => $book->estimated_reading_time,
            'difficulty_score' => $book->difficulty_score,
            'reading_difficulty' => $book->reading_difficulty,
            'cover_image_url' => $book->cover_image_url,
            'themes' => $book->themes,
            'recommendation_reasons' => $this->getRecommendationReasons($book, $user),
            'match_score' => $this->calculateMatchScore($book, $user)
        ];
    }

    /**
     * Get reasons why book is recommended for user
     */
    protected function getRecommendationReasons(Book $book, User $user): array
    {
        $reasons = [];

        // Check genre match
        $favoriteGenres = $this->extractFavoriteGenres($this->getUserReadingHistory($user));
        if (in_array($book->genre, $favoriteGenres)) {
            $reasons[] = "Matches your favorite genre: {$book->genre}";
        }

        // Check difficulty appropriateness
        $userLevel = $this->determineUserReadingLevel($user);
        if (abs($book->difficulty_score - $userLevel) <= 1) {
            $reasons[] = "Perfect difficulty level for your reading skills";
        }

        // Check themes alignment
        if (!empty($book->themes)) {
            $reasons[] = "Explores themes: " . implode(', ', array_slice($book->themes, 0, 3));
        }

        // Check academic level match
        if (in_array($user->academic_level, $book->academic_levels ?? [])) {
            $reasons[] = "Aligned with your academic level";
        }

        return $reasons;
    }

    /**
     * Calculate match score for recommendation
     */
    protected function calculateMatchScore(Book $book, User $user): float
    {
        $score = 0;
        $maxScore = 100;

        // Genre matching (30 points)
        $favoriteGenres = $this->extractFavoriteGenres($this->getUserReadingHistory($user));
        if (in_array($book->genre, $favoriteGenres)) {
            $score += 30;
        }

        // Difficulty matching (25 points)
        $userLevel = $this->determineUserReadingLevel($user);
        $difficultyDiff = abs($book->difficulty_score - $userLevel);
        if ($difficultyDiff <= 1) {
            $score += 25;
        } elseif ($difficultyDiff <= 2) {
            $score += 15;
        }

        // Academic level matching (20 points)
        if (in_array($user->academic_level, $book->academic_levels ?? [])) {
            $score += 20;
        }

        // Age appropriateness (15 points)
        if ($user->age && $book->isSuitableForAge($user->age)) {
            $score += 15;
        }

        // Reading time appropriateness (10 points)
        if ($book->estimated_reading_time <= 20) { // Under 20 hours
            $score += 10;
        }

        return round(($score / $maxScore) * 100, 1);
    }

    /**
     * Calculate adaptive difficulty based on previous performance
     */
    protected function calculateAdaptiveDifficulty(Collection $previousQuizzes, string $requestedDifficulty): string
    {
        if ($previousQuizzes->isEmpty()) {
            return $requestedDifficulty;
        }

        $averageScore = $previousQuizzes->avg(function($quiz) {
            return $quiz->results['percentage'] ?? 0;
        });

        $recentTrend = $this->calculatePerformanceTrend($previousQuizzes);

        // Adapt based on performance
        if ($averageScore >= 85 && $recentTrend >= 0) {
            // Performing well, can increase difficulty
            return $this->increaseDifficulty($requestedDifficulty);
        } elseif ($averageScore <= 65 || $recentTrend < -10) {
            // Struggling, should decrease difficulty
            return $this->decreaseDifficulty($requestedDifficulty);
        }

        return $requestedDifficulty;
    }

    /**
     * Calculate performance trend from recent quizzes
     */
    protected function calculatePerformanceTrend(Collection $quizzes): float
    {
        $recent = $quizzes->sortByDesc('completed_at')->take(3);

        if ($recent->count() < 2) {
            return 0;
        }

        $scores = $recent->pluck('results.percentage')->filter()->values();

        if ($scores->count() < 2) {
            return 0;
        }

        // Simple linear trend calculation
        $firstHalf = $scores->take(ceil($scores->count() / 2))->avg();
        $secondHalf = $scores->skip(ceil($scores->count() / 2))->avg();

        return $secondHalf - $firstHalf;
    }

    /**
     * Increase difficulty level
     */
    protected function increaseDifficulty(string $current): string
    {
        return match($current) {
            'easy' => 'medium',
            'medium' => 'hard',
            'hard' => 'hard', // Cap at hard
            default => $current
        };
    }

    /**
     * Decrease difficulty level
     */
    protected function decreaseDifficulty(string $current): string
    {
        return match($current) {
            'hard' => 'medium',
            'medium' => 'easy',
            'easy' => 'easy', // Cap at easy
            default => $current
        };
    }

    /**
     * Identify weak areas from previous quiz results
     */
    protected function identifyWeakAreas(Collection $previousQuizzes): array
    {
        $weakAreas = [];

        foreach ($previousQuizzes as $quiz) {
            $questionDetails = $quiz->results['question_details'] ?? [];

            foreach ($questionDetails as $detail) {
                if (!($detail['is_correct'] ?? true)) {
                    $questionType = $detail['question_type'] ?? 'unknown';
                    $weakAreas[$questionType] = ($weakAreas[$questionType] ?? 0) + 1;
                }
            }
        }

        // Return areas where user got wrong answers most frequently
        arsort($weakAreas);
        return array_slice(array_keys($weakAreas), 0, 3);
    }

    /**
     * Identify focus areas for adaptive learning
     */
    protected function identifyFocusAreas(User $user, Book $book, array $weakAreas): array
    {
        $focusAreas = [];

        // Focus on chapters where user had difficulties
        $chapterPerformance = [];
        $userQuizzes = QuizSession::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'completed')
            ->whereNotNull('chapter_id')
            ->get();

        foreach ($userQuizzes as $quiz) {
            $chapterId = $quiz->chapter_id;
            $score = $quiz->results['percentage'] ?? 0;

            if (!isset($chapterPerformance[$chapterId])) {
                $chapterPerformance[$chapterId] = [];
            }
            $chapterPerformance[$chapterId][] = $score;
        }

        // Find chapters with low average performance
        foreach ($chapterPerformance as $chapterId => $scores) {
            $avgScore = array_sum($scores) / count($scores);
            if ($avgScore < 70) {
                $chapter = BookChapter::find($chapterId);
                if ($chapter) {
                    $focusAreas[] = [
                        'type' => 'chapter',
                        'id' => $chapterId,
                        'title' => $chapter->title,
                        'average_score' => $avgScore
                    ];
                }
            }
        }

        return $focusAreas;
    }

    /**
     * Get user's performance history with a book
     */
    protected function getUserPerformanceHistory(User $user, Book $book): array
    {
        $quizzes = QuizSession::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'completed')
            ->orderBy('completed_at')
            ->get();

        return $quizzes->map(function($quiz) {
            return [
                'date' => $quiz->completed_at->toDateString(),
                'score' => $quiz->results['percentage'] ?? 0,
                'question_count' => $quiz->results['total_questions'] ?? 0,
                'time_taken' => $quiz->time_taken ?? 0,
                'difficulty' => $quiz->difficulty
            ];
        })->toArray();
    }

    /**
     * Generate quiz with adaptive context
     */
    protected function generateQuizWithContext(Book $book, array $context, User $user): array
    {
        $prompt = $this->buildAdaptiveQuizPrompt($book, $context, $user);

        $chatParameters = [
            'message' => $prompt,
            'age' => $user->age,
            'academic_level' => $user->academic_level,
            'subject' => 'language_arts',
            'topics' => ['adaptive_learning', 'reading_comprehension'],
            'learning_style' => $user->learning_style,
            'response_format' => 'structured',
            'difficulty' => $context['adaptive_difficulty'],
            'creativity_level' => 0.6,
            'response_length' => 2000
        ];

        $result = $this->chatService->chat($chatParameters);

        if (!$result['success']) {
            throw new \Exception('Failed to generate adaptive quiz: ' . $result['error']);
        }

        return $this->parseAdaptiveQuizResponse($result['content'], $context);
    }

/**
 * Build enhanced adaptive quiz prompt with comprehensive context
 */
protected function buildAdaptiveQuizPrompt(Book $book, array $context, User $user): string
{
    $prompt = "Generate an adaptive quiz for \"{$book->title}\" by {$book->author}.\n\n";

    $prompt .= "BOOK DETAILS:\n";
    $prompt .= "- Title: {$book->title}\n";
    $prompt .= "- Author: {$book->author}\n";
    $prompt .= "- Genre: {$book->genre}\n";
    $prompt .= "- Difficulty Score: {$book->difficulty_score}/10\n";
    $prompt .= "- Estimated Reading Time: {$book->estimated_reading_time} hours\n";

    if (!empty($book->themes)) {
        $prompt .= "- Themes: " . implode(', ', $book->themes) . "\n";
    }

    $prompt .= "\nUSER PROFILE:\n";
    $prompt .= "- Age: {$user->age}\n";
    $prompt .= "- Academic Level: {$user->academic_level}\n";
    $prompt .= "- Learning Style: {$user->learning_style}\n";
    $prompt .= "- Reading Level: " . $this->determineUserReadingLevel($user) . "/10\n";

    $prompt .= "\nADAPTATION CONTEXT:\n";
    $prompt .= "- Requested Difficulty: {$context['difficulty']}\n";
    $prompt .= "- Adaptive Difficulty: {$context['adaptive_difficulty']}\n";

    if (!empty($context['weak_concepts'])) {
        $prompt .= "- Focus on weak areas: " . implode(', ', $context['weak_concepts']) . "\n";
    }

    if (!empty($context['focus_areas'])) {
        $prompt .= "- Problem areas from previous quizzes:\n";
        foreach ($context['focus_areas'] as $area) {
            $prompt .= "  * {$area['title']} (avg score: {$area['average_score']}%)\n";
        }
    }

    if (!empty($context['focus_topics'])) {
        $prompt .= "- Focus Topics: " . implode(', ', $context['focus_topics']) . "\n";
    }

    $prompt .= "\nPERFORMANCE HISTORY:\n";
    if (!empty($context['user_performance_history'])) {
        $recentQuizzes = array_slice($context['user_performance_history'], -3);
        foreach ($recentQuizzes as $quiz) {
            $prompt .= "- {$quiz['date']}: {$quiz['score']}% ({$quiz['question_count']} questions)\n";
        }
    } else {
        $prompt .= "- No previous performance data available\n";
    }

    $prompt .= "\nQUIZ REQUIREMENTS:\n";
    $prompt .= "- Generate {$context['question_count']} questions\n";
    $prompt .= "- Question type: {$context['question_type']}\n";
    $prompt .= "- Adapt question complexity based on user's weak areas\n";
    $prompt .= "- Include scaffolding questions if user is struggling\n";
    $prompt .= "- Provide detailed explanations for learning\n";

    if ($context['include_quotes'] ?? false) {
        $prompt .= "- Include relevant book quotes in questions where appropriate\n";
    }

    $prompt .= "\nOUTPUT FORMAT:\n";
    $prompt .= "Return a JSON object with the following structure:\n";
    $prompt .= "{\n";
    $prompt .= "  \"quiz_session\": {\n";
    $prompt .= "    \"book_title\": \"string\",\n";
    $prompt .= "    \"author\": \"string\",\n";
    $prompt .= "    \"context\": \"string\"\n";
    $prompt .= "  },\n";
    $prompt .= "  \"questions\": [\n";
    $prompt .= "    {\n";
    $prompt .= "      \"question\": \"string\",\n";
    $prompt .= "      \"type\": \"multiple_choice|true_false|essay\",\n";
    $prompt .= "      \"options\": [\"string\"],\n";
    $prompt .= "      \"correct_answer\": \"string\",\n";
    $prompt .= "      \"explanation\": \"string\",\n";
    $prompt .= "      \"difficulty\": \"easy|medium|hard\",\n";
    $prompt .= "      \"points\": \"integer\",\n";
    $prompt .= "      \"learning_objective\": \"string\"\n";
    $prompt .= "    }\n";
    $prompt .= "  ]\n";
    $prompt .= "}\n";

    return $prompt;
}

/**
 * Parse adaptive quiz response with enhanced error handling
 */
protected function parseAdaptiveQuizResponse(string $content, array $context): array
{
    // Try to extract JSON from the response
    $jsonStart = strpos($content, '{');
    $jsonEnd = strrpos($content, '}');

    if ($jsonStart !== false && $jsonEnd !== false) {
        $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);

        try {
            $parsed = json_decode($jsonString, true);

            if (is_array($parsed) && isset($parsed['questions'])) {
                return [
                    'quiz_session' => $parsed['quiz_session'] ?? [],
                    'questions' => $parsed['questions'],
                    'adaptive_features' => [
                        'difficulty_adjusted' => $context['adaptive_difficulty'],
                        'focus_areas_addressed' => $context['focus_areas'] ?? [],
                        'scaffolding_included' => true
                    ],
                    'metadata' => [
                        'generation_type' => 'adaptive',
                        'user_level' => $context['user_level'] ?? 'intermediate'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse quiz JSON', [
                'content' => $content,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Fallback parsing - extract questions manually
    return $this->parseQuizManually($content, $context);
}

/**
 * Fallback method to parse quiz manually from unstructured response
 */
protected function parseQuizManually(string $content, array $context): array
{
    // This would implement a more sophisticated parsing algorithm
    // to extract questions from unstructured text

    return [
        'quiz_session' => [
            'book_title' => $context['book_title'] ?? '',
            'author' => $context['author'] ?? '',
            'context' => 'Adaptive quiz based on user performance'
        ],
        'questions' => [],
        'adaptive_features' => [
            'difficulty_adjusted' => $context['adaptive_difficulty'],
            'focus_areas_addressed' => $context['focus_areas'] ?? [],
            'scaffolding_included' => true
        ],
        'metadata' => [
            'generation_type' => 'adaptive',
            'user_level' => $context['user_level'] ?? 'intermediate'
        ]
    ];
}

    /**
     * Build discussion questions prompt
     */
    protected function buildDiscussionQuestionsPrompt(array $context): string
    {
        $prompt = "Generate engaging discussion questions for \"{$context['book_title']}\" by {$context['author']}.\n\n";

        $prompt .= "CONTEXT:\n";
        $prompt .= "- Genre: {$context['genre']}\n";
        $prompt .= "- Academic Level: {$context['academic_level']}\n";
        $prompt .= "- Discussion Focus: {$context['discussion_focus']}\n";
        $prompt .= "- Group Size: {$context['group_size']} group\n";
        $prompt .= "- Time Available: {$context['time_available']} minutes\n";

        if (!empty($context['themes'])) {
            $prompt .= "- Key Themes: " . implode(', ', $context['themes']) . "\n";
        }

        $prompt .= "\nGENERATE:\n";
        $prompt .= "1. 5-7 discussion questions of varying complexity\n";
        $prompt .= "2. Include warm-up questions and deeper analysis questions\n";
        $prompt .= "3. Provide follow-up prompts for each question\n";
        $prompt .= "4. Suggest discussion formats (pairs, small groups, whole class)\n";
        $prompt .= "5. Include questions that connect to students' lives\n";

        return $prompt;
    }

    /**
     * Parse discussion questions from AI response
     */
    protected function parseDiscussionQuestions(string $content, array $context): array
    {
        // Implementation would parse the AI response into structured discussion questions
        return [
            'warm_up_questions' => [],
            'analysis_questions' => [],
            'personal_connection_questions' => [],
            'creative_questions' => [],
            'discussion_formats' => []
        ];
    }

    /**
     * Estimate user's reading speed
     */
    protected function estimateUserReadingSpeed(User $user, Book $book): float
    {
        // Get user's historical reading data
        $readingData = $user->readingProgress()
            ->where('reading_speed', '>', 0)
            ->get();

        if ($readingData->isNotEmpty()) {
            $avgSpeed = $readingData->avg('reading_speed');

            // Adjust for book difficulty
            $difficultyAdjustment = 1 - (($book->difficulty_score - 5) * 0.1);
            return $avgSpeed * $difficultyAdjustment;
        }

        // Default speeds by academic level (pages per hour)
        $defaultSpeeds = [
            'elementary' => 15,
            'middle_school' => 20,
            'high_school' => 25,
            'college' => 30,
            'graduate' => 35
        ];

        $baseSpeed = $defaultSpeeds[$user->academic_level] ?? 25;
        $difficultyAdjustment = 1 - (($book->difficulty_score - 5) * 0.1);

        return $baseSpeed * $difficultyAdjustment;
    }

    /**
     * Create reading milestones for a book
     */
    protected function createReadingMilestones(Book $book, int $targetDays): array
    {
        $chapters = $book->chapters()->orderBy('chapter_number')->get();

        if ($chapters->isEmpty()) {
            // Create page-based milestones
            return $this->createPageBasedMilestones($book, $targetDays);
        }

        // Create chapter-based milestones
        $milestones = [];
        $chaptersPerMilestone = max(1, ceil($chapters->count() / min(10, $targetDays)));

        $currentMilestone = 1;
        $chapterGroups = $chapters->chunk($chaptersPerMilestone);

        foreach ($chapterGroups as $group) {
            $startChapter = $group->first();
            $endChapter = $group->last();

            $milestones[] = [
                'milestone_number' => $currentMilestone,
                'title' => $group->count() === 1
                    ? "Complete Chapter {$startChapter->chapter_number}: {$startChapter->title}"
                    : "Complete Chapters {$startChapter->chapter_number}-{$endChapter->chapter_number}",
                'chapters' => $group->pluck('title')->toArray(),
                'page_start' => $startChapter->page_start,
                'page_end' => $endChapter->page_end,
                'estimated_days' => ceil($targetDays * ($group->count() / $chapters->count())),
                'key_concepts' => $group->pluck('key_concepts')->flatten()->unique()->take(5)->toArray()
            ];

            $currentMilestone++;
        }

        return $milestones;
    }

    /**
     * Create page-based milestones when chapters aren't available
     */
    protected function createPageBasedMilestones(Book $book, int $targetDays): array
    {
        $totalPages = $book->total_pages;
        $milestonesCount = min(10, $targetDays);
        $pagesPerMilestone = ceil($totalPages / $milestonesCount);

        $milestones = [];
        for ($i = 0; $i < $milestonesCount; $i++) {
            $startPage = ($i * $pagesPerMilestone) + 1;
            $endPage = min($totalPages, ($i + 1) * $pagesPerMilestone);

            $milestones[] = [
                'milestone_number' => $i + 1,
                'title' => "Read pages {$startPage}-{$endPage}",
                'page_start' => $startPage,
                'page_end' => $endPage,
                'estimated_days' => ceil($targetDays / $milestonesCount),
                'page_count' => $endPage - $startPage + 1
            ];
        }

        return $milestones;
    }

    /**
 * Check for quiz-related achievements
 */
protected function checkQuizAchievements(User $user, array $quizData): array
{
    $achievements = [];
    $quizResults = $quizData['results'] ?? [];

    // Get total quizzes completed
    $totalQuizzes = QuizSession::where('user_id', $user->id)
        ->where('status', 'completed')
        ->count();

    // Perfect score achievement
    if (isset($quizResults['percentage']) && $quizResults['percentage'] >= 95) {
        $achievements[] = [
            'type' => 'perfect_score',
            'name' => 'Perfect Score!',
            'description' => 'Achieved 95% or higher on a quiz',
            'criteria' => ['score' => $quizResults['percentage']]
        ];
    }

    // High score achievement
    if (isset($quizResults['percentage']) && $quizResults['percentage'] >= 90) {
        $achievements[] = [
            'type' => 'high_score',
            'name' => 'High Achiever',
            'description' => 'Scored 90% or higher on a quiz',
            'criteria' => ['score' => $quizResults['percentage']]
        ];
    }

    // First quiz achievement
    if ($totalQuizzes === 1) {
        $achievements[] = [
            'type' => 'first_quiz',
            'name' => 'First Quiz Completed',
            'description' => 'Completed your first book quiz',
            'criteria' => ['quiz_count' => 1]
        ];
    }

    // Quiz master achievement
    if ($totalQuizzes >= 10) {
        $achievements[] = [
            'type' => 'quiz_master',
            'name' => 'Quiz Master',
            'description' => 'Completed 10 or more quizzes',
            'criteria' => ['quiz_count' => $totalQuizzes]
        ];
    }

    // Consistent performer achievement
    $recentQuizzes = QuizSession::where('user_id', $user->id)
        ->where('status', 'completed')
        ->where('completed_at', '>=', now()->subWeek())
        ->count();

    if ($recentQuizzes >= 3) {
        $achievements[] = [
            'type' => 'consistent_performer',
            'name' => 'Consistent Performer',
            'description' => 'Completed 3 or more quizzes in one week',
            'criteria' => ['recent_quiz_count' => $recentQuizzes]
        ];
    }

    return $achievements;
}

    public function getNextLearningSteps($user, $book, $quizResults)
    {
        // Placeholder implementation - return generic suggestions
        $suggestions = [];

        $percentage = $quizResults['percentage'] ?? 0;

        if ($percentage < 70) {
            $suggestions[] = "Review the book chapters again";
            $suggestions[] = "Take notes while reading";
        } elseif ($percentage < 85) {
            $suggestions[] = "Explore related books by the same author";
            $suggestions[] = "Join a book discussion group";
        } else {
            $suggestions[] = "Try more challenging books";
            $suggestions[] = "Explore critical analysis of this work";
        }

        return $suggestions;
    }


    /**
     * Additional helper methods would continue here...
     * Including methods for:
     * - generateReadingStrategies()
     * - generateWeeklyGoals()
     * - generateComprehensionCheckpoints()
     * - buildVocabularyPrompt()
     * - parseVocabularySupport()
     * - getFallbackVocabularySupport()
     * - checkQuizAchievements()
     * - checkReadingAchievements()
     * - checkEngagementAchievements()
     * And many more...
     */
}
