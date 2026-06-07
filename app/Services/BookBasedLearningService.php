<?php

namespace App\Services;

use App\Models\Book;
use App\Models\QuizSession;
use App\Models\ReadingAchievement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BookBasedLearningService
{
    protected $chatService;

    public function __construct(ResearchAssistantService $chatService)
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
        if (! empty($favoriteGenres)) {
            $query->whereIn('genre', $favoriteGenres);
        }

        // Exclude already read books
        $readBookIds = $user->readingProgress()->pluck('book_id')->toArray();
        if (! empty($readBookIds)) {
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
     * Get user's reading history with performance data
     */
    protected function getUserReadingHistory(User $user): Collection
    {
        return $user->readingProgress()
            ->with(['book', 'book.reviews' => function ($query) use ($user) {
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

        $averageScore = $quizzes->avg(function ($quiz) {
            return $quiz->results['percentage'] ?? 0;
        });

        // Convert percentage to difficulty scale (1-10)
        if ($averageScore >= 90) {
            return 8;
        } // Advanced
        if ($averageScore >= 80) {
            return 6;
        } // Intermediate-Advanced
        if ($averageScore >= 70) {
            return 5;
        } // Intermediate
        if ($averageScore >= 60) {
            return 4;
        } // Beginner-Intermediate

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
            'match_score' => $this->calculateMatchScore($book, $user),
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
            $reasons[] = 'Perfect difficulty level for your reading skills';
        }

        // Check themes alignment
        if (! empty($book->themes)) {
            $reasons[] = 'Explores themes: '.implode(', ', array_slice($book->themes, 0, 3));
        }

        // Check academic level match
        if (in_array($user->academic_level, $book->academic_levels ?? [])) {
            $reasons[] = 'Aligned with your academic level';
        }

        return $reasons;
    }

    // ==========================================
    // PROTECTED HELPER METHODS
    // ==========================================

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
     * Generate adaptive quiz based on user's performance
     */
    public function generateAdaptiveQuiz(User $user, ?Book $book, array $parameters): array
    {
        // Check if we're working with file content instead of a book
        $isFileBased = ! empty($parameters['file_content']);

        if ($isFileBased) {
            // For file-based quizzes, we don't have previous performance or focus areas
            $previousQuizzes = collect();
            $adaptiveDifficulty = $parameters['difficulty'] ?? 'medium';
            $weakAreas = [];
            $focusAreas = [];
            $userPerformanceHistory = [];
        } else {
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

            // Get user performance history
            $userPerformanceHistory = $this->getUserPerformanceHistory($user, $book);
        }

        // Build context with adaptive parameters
        $adaptiveContext = array_merge($parameters, [
            'adaptive_difficulty' => $adaptiveDifficulty,
            'focus_areas' => $focusAreas,
            'weak_concepts' => $weakAreas,
            'user_performance_history' => $userPerformanceHistory,
        ]);

        return $this->generateQuizWithContext($book, $adaptiveContext, $user);
    }

    /**
     * Calculate adaptive difficulty based on previous performance
     */
    protected function calculateAdaptiveDifficulty(Collection $previousQuizzes, string $requestedDifficulty): string
    {
        if ($previousQuizzes->isEmpty()) {
            return $requestedDifficulty;
        }

        $averageScore = $previousQuizzes->avg(function ($quiz) {
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
        return match ($current) {
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
        return match ($current) {
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
                if (! ($detail['is_correct'] ?? true)) {
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
    protected function identifyFocusAreas(User $user, ?Book $book, array $weakAreas): array
    {

        if (! $book) {
            return [];
        }
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

            if (! isset($chapterPerformance[$chapterId])) {
                $chapterPerformance[$chapterId] = [];
            }
            $chapterPerformance[$chapterId][] = $score;
        }

        // Find chapters with low average performance
        foreach ($chapterPerformance as $chapterId => $scores) {
            $avgScore = array_sum($scores) / count($scores);
            if ($avgScore < 70) {
                // Get the book's table of content
                $toc = $book->table_of_contents;

                if ($toc) {
                    // Format the contents directly since $toc is already the content array
                    $formattedContents = collect($toc)->map(function ($chapter) {
                        return [
                            'chapter_number' => $chapter['chapter'] ?? 1,
                            'title' => $chapter['title'] ?? 'Untitled Chapter',
                            'description' => $chapter['description'] ?? '',
                            'page_range' => isset($chapter['page_start'], $chapter['page_end'])
                                ? "Pages {$chapter['page_start']}-{$chapter['page_end']}"
                                : '',
                            'page_count' => isset($chapter['page_start'], $chapter['page_end'])
                                ? $chapter['page_end'] - $chapter['page_start'] + 1
                                : 0,
                            'sections' => $chapter['sections'] ?? [],
                        ];
                    })->toArray();

                    $chapterInfo = collect($formattedContents)->firstWhere('chapter_number', $chapterId);

                    if ($chapterInfo) {
                        $focusAreas[] = [
                            'type' => 'chapter',
                            'id' => $chapterId,
                            'title' => $chapterInfo['title'],
                            'average_score' => $avgScore,
                        ];
                    }
                }
            }
        }

        return $focusAreas;
    }

    /**
     * Get user's performance history with a book
     */
    protected function getUserPerformanceHistory(User $user, ?Book $book): array
    {
        if (! $book) {
            return [];
        }

        $quizzes = QuizSession::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'completed')
            ->orderBy('completed_at')
            ->get();

        return $quizzes->map(function ($quiz) {
            return [
                'date' => $quiz->completed_at->toDateString(),
                'score' => $quiz->results['percentage'] ?? 0,
                'question_count' => $quiz->results['total_questions'] ?? 0,
                'time_taken' => $quiz->time_taken ?? 0,
                'difficulty' => $quiz->difficulty,
            ];
        })->toArray();
    }

    /**
     * Generate quiz with adaptive context
     */
    protected function generateQuizWithContext(?Book $book, array $context, User $user): array
    {
        $prompt = $this->buildAdaptiveQuizPrompt($book, $context, $user);

        // FIX: More realistic token calculation
        $questionCount = $context['question_count'] ?? 10;

        // Token breakdown:
        // - Each question with 4 options + explanation: ~200-250 tokens
        // - Metadata/formatting: ~500 tokens
        // - Safety buffer: 20%
        $tokensPerQuestion = 5000;
        $baseTokens = 500;
        $requiredTokens = ($questionCount * $tokensPerQuestion) + $baseTokens;

        // Add 20% safety buffer
        $maxTokens = (int) ($requiredTokens * 1.2);

        // Ensure minimum and maximum bounds
        $maxTokens = max(20000, min(160000, $maxTokens));

        $chatParameters = [
            'input' => $prompt,
            'request_type' => 'quiz_generation',
            'creativity_level' => 0.7,
            'response_length' => $maxTokens,
        ];

        $result = $this->chatService->chat($chatParameters);

        if (! $result['success']) {
            throw new RuntimeException('Failed to generate adaptive quiz: '.($result['error'] ?? 'Unknown error'));
        }

        return $this->parseAdaptiveQuizResponse($result['content'], $context);
    }

    /**
     * Build enhanced adaptive quiz prompt with comprehensive context
     */
    protected function buildAdaptiveQuizPrompt(?Book $book, array $context, User $user): string
    {
        $questionCount = $context['question_count'] ?? 10;

        $prompt = '';
        if (! empty($context['file_content'])) {
            $prompt = "Generate a quiz based on the following content:\n\n";
            $prompt .= substr($context['file_content'], 0, 3000)."\n\n";
            $prompt .= "CONTENT DETAILS:\n";
            if (! empty($context['file_name'])) {
                $prompt .= "- File Name: {$context['file_name']}\n";
            }
        } elseif ($book !== null && isset($book->id)) {
            $prompt = "Generate an adaptive quiz for \"{$book->title}\" by {$book->author->user?->name}.\n\n";
            $prompt .= "BOOK DETAILS:\n";
            $prompt .= "- Title: {$book->title}\n";
            $prompt .= "- Author: {$book->author_name}\n";
            $prompt .= "- Genre: {$book->genre}\n";
            $prompt .= "- Difficulty Score: {$book->difficulty_score}/10\n";
        }

        $prompt .= "\nUSER PROFILE:\n";
        $prompt .= "- Age: {$user->age}\n";
        $prompt .= "- Academic Level: {$user->academic_level}\n";

        $prompt .= "\nQUIZ REQUIREMENTS:\n";
        // CRITICAL: Make this EXTREMELY clear
        $prompt .= "⚠️ CRITICAL: You MUST generate EXACTLY {$questionCount} complete questions.\n";
        $prompt .= "⚠️ CRITICAL: Each question MUST have ALL required fields.\n";
        $prompt .= "⚠️ DO NOT stop generating until all {$questionCount} questions are complete.\n\n";

        $prompt .= "- Total Questions Required: {$questionCount}\n";
        $prompt .= "- Question Type: {$context['question_type']}\n";
        $prompt .= "- Difficulty: {$context['difficulty']}\n";

        if ($context['include_quotes'] ?? false) {
            $prompt .= "- Include relevant quotes where appropriate\n";
        }

        // CRITICAL: Simplify the JSON structure requirement
        $prompt .= "\n=== RESPONSE FORMAT (JSON ONLY) ===\n";
        $prompt .= "Return ONLY valid JSON with this EXACT structure:\n\n";
        $prompt .= "{\n";
        $prompt .= '  "quiz_session": {'."\n";
        $prompt .= '    "book_title": "string",'."\n";
        $prompt .= '    "author": "string",'."\n";
        $prompt .= '    "context": "string"'."\n";
        $prompt .= "  },\n";
        $prompt .= '  "questions": ['."\n";

        // Show example for EACH question type
        if ($context['question_type'] === 'multiple_choice' || $context['question_type'] === 'mixed') {
            $prompt .= "    // Question 1 example:\n";
            $prompt .= "    {\n";
            $prompt .= '      "question": "What is the primary focus of the book?",'."\n";
            $prompt .= '      "type": "multiple_choice",'."\n";
            $prompt .= '      "options": ["Option A", "Option B", "Option C", "Option D"],'."\n";
            $prompt .= '      "correct_answer": "Option B",'."\n";
            $prompt .= '      "explanation": "Explanation here",'."\n";
            $prompt .= '      "difficulty": "medium",'."\n";
            $prompt .= '      "points": 1'."\n";
            $prompt .= "    },\n";
        }

        if ($context['question_type'] === 'true_false' || $context['question_type'] === 'mixed') {
            $prompt .= "    // Question 2 example:\n";
            $prompt .= "    {\n";
            $prompt .= '      "question": "The author emphasizes public interaction?",'."\n";
            $prompt .= '      "type": "true_false",'."\n";
            $prompt .= '      "options": ["True", "False"],'."\n";
            $prompt .= '      "correct_answer": "True",'."\n";
            $prompt .= '      "explanation": "Explanation here",'."\n";
            $prompt .= '      "difficulty": "easy",'."\n";
            $prompt .= '      "points": 1'."\n";
            $prompt .= "    },\n";
        }

        // FIX: Add essay question example with proper structure
        if ($context['question_type'] === 'essay' || $context['question_type'] === 'mixed') {
            $prompt .= "    // Essay Question example:\n";
            $prompt .= "    {\n";
            $prompt .= '      "question": "Discuss the main themes presented in the book. How do they relate to modern society?",'."\n";
            $prompt .= '      "type": "essay",'."\n";
            $prompt .= '      "correct_answer": "The expected answer should discuss themes such as identity, social justice, and human connection. Students should provide specific examples from the text and relate them to contemporary issues.",'."\n";
            $prompt .= '      "explanation": "A strong essay answer will identify key themes, provide textual evidence, and make meaningful connections to current events or personal experiences.",'."\n";
            $prompt .= '      "difficulty": "hard",'."\n";
            $prompt .= '      "points": 3'."\n";
            $prompt .= "    },\n";
        }

        $prompt .= "    // ... continue for ALL {$questionCount} questions\n";
        $prompt .= "  ]\n";
        $prompt .= "}\n\n";

        $prompt .= "IMPORTANT GENERATION RULES:\n";
        $prompt .= "1. Generate ALL {$questionCount} questions - do not stop early\n";
        $prompt .= "2. Each question MUST be complete with all fields\n";
        $prompt .= "3. Number your questions mentally (1 through {$questionCount})\n";
        $prompt .= "4. For multiple_choice: provide exactly 4 options\n";
        $prompt .= "5. For true_false: options are [\"True\", \"False\"]\n";
        // FIX: Add clear instruction for essay questions
        $prompt .= "6. For essay questions:\n";
        $prompt .= "   - 'question' field contains the actual question prompt\n";
        $prompt .= "   - 'correct_answer' field contains the expected/model answer\n";
        $prompt .= "   - Do NOT include 'options' field\n";
        $prompt .= "   - Set 'points' to 2-5 based on complexity\n";
        $prompt .= "7. Do NOT include markdown (no ```json)\n";
        $prompt .= "8. Do NOT include any text before or after the JSON\n";
        $prompt .= "9. Start with { and end with }\n\n";

        $prompt .= "BEGIN GENERATION NOW. Generate all {$questionCount} questions:\n";

        return $prompt;
    }

    /**
     * Parse adaptive quiz response with enhanced error handling
     */
    protected function parseAdaptiveQuizResponse(string $content, array $context): array
    {
        // Simply try to decode the entire content as JSON
        $parsed = json_decode($content, true);

        // If successful and has the structure we need, return it
        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
            return $this->validateAndReturnQuizStructure($parsed, $context);
        }

        // If direct decode fails, try to find and extract JSON
        $jsonString = $this->extractJsonString($content);

        if ($jsonString) {
            $parsed = json_decode($jsonString, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                return $this->validateAndReturnQuizStructure($parsed, $context);
            }
        }

        Log::error('Failed to parse quiz response', [
            'json_error' => json_last_error_msg(),
            'content_preview' => substr($content, 0, 500),
        ]);

        return $this->parseQuizManually($content, $context);
    }

    /**
     * Validate and return quiz structure in the expected format
     */
    protected function validateAndReturnQuizStructure(array $parsed, array $context): array
    {
        // CASE 1: Already has the complete structure with quiz_session and questions
        if (isset($parsed['quiz_session']) && isset($parsed['questions']) && is_array($parsed['questions'])) {
            Log::info('Found complete quiz structure', [
                'has_quiz_session' => true,
                'question_count' => count($parsed['questions']),
            ]);

            return [
                'quiz_session' => $parsed['quiz_session'],
                'questions' => $this->normalizeQuestions($parsed['questions']),
                'adaptive_features' => $parsed['adaptive_features'] ?? [
                    'difficulty_adjusted' => $context['adaptive_difficulty'] ?? false,
                    'focus_areas_addressed' => $context['focus_areas'] ?? [],
                    'scaffolding_included' => true,
                ],
                'metadata' => $parsed['metadata'] ?? [
                    'generation_type' => 'adaptive',
                    'user_level' => $context['user_level'] ?? 'intermediate',
                    'questions_generated' => count($parsed['questions']),
                    'questions_requested' => $context['question_count'] ?? 10,
                ],
            ];
        }

        // CASE 2: Has nested questions array
        if (isset($parsed['questions']) && is_array($parsed['questions'])) {
            Log::info('Found nested questions array', [
                'question_count' => count($parsed['questions']),
            ]);

            return [
                'quiz_session' => [
                    'book_title' => $context['book_title'] ?? 'Quiz',
                    'author' => $context['author'] ?? 'Unknown',
                    'context' => 'Generated quiz',
                ],
                'questions' => $this->normalizeQuestions($parsed['questions']),
                'adaptive_features' => [
                    'difficulty_adjusted' => $context['adaptive_difficulty'] ?? false,
                    'focus_areas_addressed' => $context['focus_areas'] ?? [],
                    'scaffolding_included' => true,
                ],
                'metadata' => [
                    'generation_type' => 'adaptive',
                    'user_level' => $context['user_level'] ?? 'intermediate',
                    'questions_generated' => count($parsed['questions']),
                    'questions_requested' => $context['question_count'] ?? 10,
                ],
            ];
        }

        // CASE 3: Questions at root level (array of questions)
        if (isset($parsed[0]) && is_array($parsed[0]) && isset($parsed[0]['question'])) {
            Log::info('Found questions at root level', [
                'question_count' => count($parsed),
            ]);

            return [
                'quiz_session' => [
                    'book_title' => $context['book_title'] ?? 'Quiz',
                    'author' => $context['author'] ?? 'Unknown',
                    'context' => 'Generated quiz',
                ],
                'questions' => $this->normalizeQuestions($parsed),
                'adaptive_features' => [
                    'difficulty_adjusted' => $context['adaptive_difficulty'] ?? false,
                    'focus_areas_addressed' => $context['focus_areas'] ?? [],
                    'scaffolding_included' => true,
                ],
                'metadata' => [
                    'generation_type' => 'adaptive',
                    'user_level' => $context['user_level'] ?? 'intermediate',
                    'questions_generated' => count($parsed),
                    'questions_requested' => $context['question_count'] ?? 10,
                ],
            ];
        }

        Log::warning('Parsed JSON but structure not recognized', [
            'keys' => array_keys($parsed),
            'is_array' => is_array($parsed),
            'count' => count($parsed),
        ]);

        // Fall back to manual parsing
        return $this->parseQuizManually(json_encode($parsed), $context);
    }

    /**
     * Normalize questions to ensure proper structure for all question types
     */
    protected function normalizeQuestions(array $questions): array
    {
        $normalized = [];

        foreach ($questions as $question) {
            if (! is_array($question)) {
                continue;
            }

            // Determine question type
            $type = $question['type'] ?? 'multiple_choice';

            // Base structure
            $normalizedQuestion = [
                'question' => $question['question'] ?? '',
                'type' => $type,
                'difficulty' => $question['difficulty'] ?? 'medium',
                'points' => $question['points'] ?? 1,
                'explanation' => $question['explanation'] ?? '',
                'learning_objective' => $question['learning_objective'] ?? '',
            ];

            // Handle type-specific fields
            switch ($type) {
                case 'multiple_choice':
                    // Ensure options array exists
                    $normalizedQuestion['options'] = $question['options'] ?? [];

                    // If options is empty, try to extract from question text
                    if (empty($normalizedQuestion['options']) && isset($question['question'])) {
                        $normalizedQuestion['options'] = $this->extractOptionsFromText($question['question']);
                    }

                    // Ensure correct_answer exists
                    $normalizedQuestion['correct_answer'] = $question['correct_answer'] ?? ($normalizedQuestion['options'][0] ?? '');
                    break;

                case 'true_false':
                    // For true/false, ensure options are set
                    $normalizedQuestion['options'] = ['True', 'False'];

                    // Normalize correct answer
                    $correctAnswer = $question['correct_answer'] ?? 'True';
                    if (is_bool($correctAnswer)) {
                        $correctAnswer = $correctAnswer ? 'True' : 'False';
                    }
                    $normalizedQuestion['correct_answer'] = ucfirst(strtolower($correctAnswer));
                    break;

                case 'essay':
                case 'essay_question':
                    // Essay questions should NOT have options
                    $normalizedQuestion['type'] = 'essay';

                    // FIX: Ensure question text is not empty
                    if (empty($normalizedQuestion['question'])) {
                        // Try to extract from other fields if question is missing
                        $normalizedQuestion['question'] = $question['prompt']
                            ?? $question['essay_prompt']
                            ?? $question['text']
                            ?? 'Please provide your answer to this question.';

                        Log::warning('Essay question missing question text, using fallback', [
                            'original_keys' => array_keys($question),
                            'fallback_used' => $normalizedQuestion['question'],
                        ]);
                    }

                    // Correct answer is the expected/model answer
                    $normalizedQuestion['correct_answer'] = $question['correct_answer'] ?? $question['answer'] ?? '';

                    // Ensure essay questions have higher point values
                    if ($normalizedQuestion['points'] < 2) {
                        $normalizedQuestion['points'] = 3;
                    }

                    // Remove options if they exist
                    unset($normalizedQuestion['options']);

                    Log::debug('Normalized essay question', [
                        'has_question' => ! empty($normalizedQuestion['question']),
                        'question_length' => strlen($normalizedQuestion['question']),
                        'has_answer' => ! empty($normalizedQuestion['correct_answer']),
                    ]);
                    break;

                default:
                    // For unknown types, include options if they exist
                    if (isset($question['options'])) {
                        $normalizedQuestion['options'] = $question['options'];
                    }
                    $normalizedQuestion['correct_answer'] = $question['correct_answer'] ?? '';
                    break;
            }

            $normalized[] = $normalizedQuestion;
        }

        Log::info('Normalized questions', [
            'original_count' => count($questions),
            'normalized_count' => count($normalized),
            'types' => array_count_values(array_column($normalized, 'type')),
        ]);

        return $normalized;
    }

    /**
     * Try to extract options from question text if they're embedded
     */
    protected function extractOptionsFromText(string $text): array
    {
        $options = [];

        // Pattern for options like "A) Option text" or "a. Option text"
        if (preg_match_all('/[A-D][\.\)]\s*(.+?)(?=[A-D][\.\)]|$)/si', $text, $matches)) {
            $options = array_map('trim', $matches[1]);
        }

        return $options;
    }

    /**
     * Fallback method to parse quiz manually from unstructured response
     */
    protected function parseQuizManually(string $content, array $context): array
    {
        Log::warning('Using manual parsing fallback');

        // Try to extract questions using regex patterns
        $questions = [];

        // Pattern 1: Look for numbered questions
        if (preg_match_all('/(?:Question\s+)?(\d+)[\.\)]\s*(.+?)(?=(?:Question\s+)?\d+[\.\)]|\Z)/si', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $questionText = trim($match[2]);

                // Try to extract options if it's multiple choice
                $options = [];
                if (preg_match_all('/(?:[A-D][\.\)]|[a-d][\.\)])\s*(.+?)(?=[A-D][\.\)]|[a-d][\.\)]|\n\n|\Z)/s', $questionText, $optionMatches)) {
                    $options = array_map('trim', $optionMatches[1]);
                }

                if (! empty($questionText)) {
                    $questions[] = [
                        'question' => $questionText,
                        'type' => ! empty($options) ? 'multiple_choice' : 'essay',
                        'options' => $options,
                        'correct_answer' => ! empty($options) ? $options[0] : '',
                        'explanation' => 'Review the material for the answer.',
                        'difficulty' => $context['difficulty'] ?? 'medium',
                        'points' => 1,
                    ];
                }
            }
        }

        Log::info('Manual parsing result', ['questions_found' => count($questions)]);

        if (empty($questions)) {
            // Generate fallback error result
            return [
                'success' => false,
                'error' => 'Failed to parse quiz questions from AI response. Please try again.',
                'error_code' => 'PARSE_ERROR',
                'questions' => [],
            ];
        }

        return [
            'quiz_session' => [
                'book_title' => $context['book_title'] ?? 'Quiz',
                'author' => $context['author'] ?? 'Unknown',
                'context' => 'Generated quiz',
            ],
            'questions' => $questions,
            'adaptive_features' => [
                'difficulty_adjusted' => $context['adaptive_difficulty'] ?? 'medium',
                'focus_areas_addressed' => $context['focus_areas'] ?? [],
                'scaffolding_included' => true,
            ],
            'metadata' => [
                'generation_type' => 'adaptive',
                'user_level' => $context['user_level'] ?? 'intermediate',
                'parsing_method' => 'manual',
            ],
        ];
    }

    /**
     * Extract JSON string from content using simple pattern matching
     */
    protected function extractJsonString(string $content): ?string
    {
        // Remove any markdown code blocks
        $content = preg_replace('/```(?:json)?\s*/', '', $content);
        $content = trim($content);

        // Find first { and last }
        $firstBrace = strpos($content, '{');
        $lastBrace = strrpos($content, '}');

        if ($firstBrace === false || $lastBrace === false || $lastBrace <= $firstBrace) {
            return null;
        }

        return substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
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
            'personalized_recommendations' => [],
        ];

        // Analyze by question types
        $questionTypePerformance = $this->analyzeQuestionTypePerformance($quizResults);

        foreach ($questionTypePerformance as $type => $performance) {
            if ($performance['accuracy'] >= 0.8) {
                $analysis['strength_areas'][] = [
                    'area' => $this->getReadableQuestionType($type),
                    'accuracy' => $performance['accuracy'],
                    'description' => $this->getStrengthDescription($type, $performance['accuracy']),
                ];
            } elseif ($performance['accuracy'] < 0.6) {
                $analysis['improvement_areas'][] = [
                    'area' => $this->getReadableQuestionType($type),
                    'accuracy' => $performance['accuracy'],
                    'recommendations' => $this->getImprovementRecommendations($type),
                    'practice_activities' => $this->getPracticeActivities($type, $user->learning_style),
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
                'criteria' => ['score' => $quizResults['percentage']],
            ];
        }

        // High score achievement
        if (isset($quizResults['percentage']) && $quizResults['percentage'] >= 90) {
            $achievements[] = [
                'type' => 'high_score',
                'name' => 'High Achiever',
                'description' => 'Scored 90% or higher on a quiz',
                'criteria' => ['score' => $quizResults['percentage']],
            ];
        }

        // First quiz achievement
        if ($totalQuizzes === 1) {
            $achievements[] = [
                'type' => 'first_quiz',
                'name' => 'First Quiz Completed',
                'description' => 'Completed your first book quiz',
                'criteria' => ['quiz_count' => 1],
            ];
        }

        // Quiz master achievement
        if ($totalQuizzes >= 10) {
            $achievements[] = [
                'type' => 'quiz_master',
                'name' => 'Quiz Master',
                'description' => 'Completed 10 or more quizzes',
                'criteria' => ['quiz_count' => $totalQuizzes],
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
                'criteria' => ['recent_quiz_count' => $recentQuizzes],
            ];
        }

        return $achievements;
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
            'time_available' => $parameters['time_minutes'] ?? 45,
        ];

        $prompt = $this->buildDiscussionQuestionsPrompt($context);

        $chatParameters = [
            'message' => $prompt,
            'academic_level' => $parameters['academic_level'] ?? 'high_school',
            'subject' => 'language_arts',
            'topics' => ['discussion', 'literary_analysis', 'critical_thinking'],
            'response_format' => 'structured',
            'creativity_level' => 1,
            'response_length' => 1200,
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            return $this->parseDiscussionQuestions($result['content'], $context);
        }

        // Fallback questions
        return $this->getFallbackDiscussionQuestions($book, $context);
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

        if (! empty($context['themes'])) {
            $prompt .= '- Key Themes: '.implode(', ', $context['themes'])."\n";
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
            'discussion_formats' => [],
        ];
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
                'estimated_reading_time' => $book->estimated_reading_time,
            ],
            'plan' => [
                'target_days' => $recommendedDays,
                'daily_page_target' => ceil($totalPages / $recommendedDays),
                'daily_time_needed' => ceil((ceil($totalPages / $recommendedDays) * 60) / $userReadingSpeed),
                'alternative_suggested' => $alternativePlan,
            ],
            'milestones' => $milestones,
            'reading_strategies' => $strategies,
            'weekly_goals' => $this->generateWeeklyGoals($milestones),
            'comprehension_checkpoints' => $this->generateComprehensionCheckpoints($book),
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
            'graduate' => 35,
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
        // Use table of contents instead of chapters relationship
        $chapters = collect();
        if ($book->table_of_contents) {
            $chapters = collect($book->table_of_contents->getFormattedContents());
        }

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
                    ? "Complete Chapter {$startChapter['chapter_number']}: {$startChapter['title']}"
                    : "Complete Chapters {$startChapter['chapter_number']}-{$endChapter['chapter_number']}",
                'chapters' => $group->pluck('title')->toArray(),
                'page_start' => '', // TOC format doesn't have direct page_start
                'page_end' => '',   // TOC format doesn't have direct page_end
                'estimated_days' => ceil($targetDays * ($group->count() / $chapters->count())),
                'key_concepts' => [], // TOC format doesn't have key_concepts
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
                'page_count' => $endPage - $startPage + 1,
            ];
        }

        return $milestones;
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
        if ($chapterId && $book->table_of_contents) {
            // Use table of contents instead of BookChapter
            $formattedContents = $book->table_of_contents->getFormattedContents();
            $chapter = collect($formattedContents)->firstWhere('chapter_number', $chapterId);
        }

        // Build vocabulary request
        $vocabPrompt = $this->buildVocabularyPrompt($book, $chapter, $parameters);

        $chatParameters = [
            'message' => $vocabPrompt,
            'academic_level' => $userLevel,
            'subject' => 'language_arts',
            'topics' => ['vocabulary', 'reading_comprehension'],
            'response_format' => 'structured',
            'creativity_level' => 1,
            'response_length' => 1000,
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            return $this->parseVocabularySupport($result['content'], $book, $chapter);
        }

        // Fallback vocabulary support
        return $this->getFallbackVocabularySupport($book, $chapter, $userLevel);
    }

    public function getNextLearningSteps($user, $book, $quizResults)
    {
        // Placeholder implementation - return generic suggestions
        $suggestions = [];

        $percentage = $quizResults['percentage'] ?? 0;

        if ($percentage < 70) {
            $suggestions[] = 'Review the book chapters again';
            $suggestions[] = 'Take notes while reading';
        } elseif ($percentage < 85) {
            $suggestions[] = 'Explore related books by the same author';
            $suggestions[] = 'Join a book discussion group';
        } else {
            $suggestions[] = 'Try more challenging books';
            $suggestions[] = 'Explore critical analysis of this work';
        }

        return $suggestions;
    }

    /**
     * Generate quiz based on book context with graceful error handling
     */
    public function generateBookQuiz(array $parameters, ?Book $book = null): array
    {
        $user = auth()->user();

        // Check token availability first
        if ($user && ! $user->hasOpenAiTokens()) {
            Log::warning('Insufficient tokens for book quiz', ['user_id' => $user->id]);

            return [
                'success' => false,
                'error' => 'Insufficient tokens. Please purchase a token package to continue.',
            ];
        }

        // Validate book context if book_id is provided
        if (isset($parameters['book_id'])) {
            if (! $book) {
                // Try to load the book if not provided
                $book = Book::with(['chapters', 'sections', 'author'])->find($parameters['book_id']);
            }

            if (! $book) {
                Log::warning('Book not found for quiz generation', [
                    'user_id' => $user?->id,
                    'book_id' => $parameters['book_id'],
                ]);

                return [
                    'success' => false,
                    'error' => 'The selected book could not be found. Please select a different book or try uploading your own content.',
                    'error_code' => 'BOOK_NOT_FOUND',
                    'fallback_available' => ! empty($parameters['file_content']),
                ];
            }

            // Validate book has sufficient content
            if ($this->bookHasInsufficientContent($book, $parameters)) {
                Log::warning('Book has insufficient content for quiz', [
                    'user_id' => $user?->id,
                    'book_id' => $book->id,
                    'has_chapters' => $book->chapters->isNotEmpty(),
                    'chapter_id' => $parameters['chapter_id'] ?? null,
                ]);

                return [
                    'success' => false,
                    'error' => 'The selected book or chapter does not have enough content to generate a quiz. Please select a different range or upload your own content.',
                    'error_code' => 'INSUFFICIENT_CONTENT',
                    'suggestions' => $this->getBookContentSuggestions($book),
                ];
            }
        }

        // If we have file content as fallback, use that instead
        if (! $book && ! empty($parameters['file_content'])) {
            Log::info('Using uploaded file content instead of book', [
                'user_id' => $user?->id,
                'file_name' => $parameters['file_name'] ?? 'unknown',
            ]);

            $parameters['message'] = $this->buildQuizPromptFromFile($parameters);
        } else {
            // Build quiz prompt with book context
            $parameters['message'] = $this->buildQuizPromptFromBook($book, $parameters);
        }

        // Use existing chat method for actual generation
        return $this->chat($parameters);
    }

    /**
     * Check if book has insufficient content for quiz generation
     */
    protected function bookHasInsufficientContent(?Book $book, array $parameters): bool
    {
        if (! $book) {
            return true;
        }

        // Check if specific chapter is requested
        if (isset($parameters['chapter_id'])) {
            $chapter = $book->chapters()->find($parameters['chapter_id']);

            if (! $chapter) {
                return true;
            }

            // Check if chapter has content (adjust threshold as needed)
            $contentLength = strlen($chapter->content ?? '');

            return $contentLength < 100; // Minimum 100 characters
        }

        // Check if book has any chapters or sections
        if ($book->chapters->isEmpty() && $book->sections->isEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Get suggestions for book content issues
     */
    protected function getBookContentSuggestions(Book $book): array
    {
        $suggestions = [];

        if ($book->chapters->isNotEmpty()) {
            $suggestions[] = 'Try selecting one of the available chapters: '.
                $book->chapters->pluck('title')->take(3)->implode(', ');
        }

        if ($book->sections->isNotEmpty()) {
            $suggestions[] = 'Try selecting from available sections';
        }

        $suggestions[] = 'Upload a text file or document with content from this book';
        $suggestions[] = 'Select a different book from the library';

        return $suggestions;
    }

    /**
     * Build quiz prompt from uploaded file
     */
    protected function buildQuizPromptFromFile(array $parameters): string
    {
        $prompt = "Generate a {$parameters['difficulty']} difficulty quiz with {$parameters['question_count']} questions ";
        $prompt .= "of type {$parameters['question_type']} based on the following content:\n\n";

        if (! empty($parameters['file_name'])) {
            $prompt .= "Source: {$parameters['file_name']}\n\n";
        }

        $prompt .= "Content:\n{$parameters['file_content']}\n\n";

        if (! empty($parameters['focus_topics'])) {
            $prompt .= 'Focus on these topics: '.implode(', ', $parameters['focus_topics'])."\n";
        }

        $prompt .= "\nGenerate questions that test comprehension and understanding of the material.";

        return $prompt;
    }

    /**
     * Build quiz prompt from book context
     */
    protected function buildQuizPromptFromBook(Book $book, array $parameters): string
    {
        $prompt = "Generate a {$parameters['difficulty']} difficulty quiz with {$parameters['question_count']} questions ";
        $prompt .= "of type {$parameters['question_type']} based on the following book:\n\n";
        $prompt .= "Book Title: {$book->title}\n";
        $prompt .= 'Author: '.($book->author->name ?? 'Unknown')."\n";

        if (isset($parameters['chapter_id']) && $book->chapters->isNotEmpty()) {
            $chapter = $book->chapters()->find($parameters['chapter_id']);
            if ($chapter) {
                $prompt .= "Chapter: {$chapter->title}\n";
                $prompt .= "Chapter Content:\n{$chapter->content}\n\n";
            }
        } elseif ($book->description) {
            $prompt .= "Book Description: {$book->description}\n\n";
        }

        if (! empty($parameters['focus_topics'])) {
            $prompt .= 'Focus on these topics: '.implode(', ', $parameters['focus_topics'])."\n";
        }

        if (! empty($parameters['page_start']) && ! empty($parameters['page_end'])) {
            $prompt .= "Focus on pages {$parameters['page_start']} to {$parameters['page_end']}\n";
        }

        $prompt .= "\nGenerate questions that test comprehension and understanding of the material.";

        if ($parameters['include_quotes'] ?? false) {
            $prompt .= ' Include relevant quotes from the book in the questions where appropriate.';
        }

        return $prompt;
    }

    /**
     * Try to repair truncated JSON by adding missing closing brackets
     */
    protected function tryRepairTruncatedJson(string $content, int $start, string $startChar, string $endChar): string|false
    {
        $depth = 0;
        $length = strlen($content);
        $inString = false;
        $escapeNext = false;

        // Count the bracket depth
        for ($i = $start; $i < $length; $i++) {
            $char = $content[$i];

            if ($escapeNext) {
                $escapeNext = false;

                continue;
            }

            if ($char === '\\') {
                $escapeNext = true;

                continue;
            }

            if ($char === '"') {
                $inString = ! $inString;

                continue;
            }

            if (! $inString) {
                if ($char === $startChar) {
                    $depth++;
                } elseif ($char === $endChar) {
                    $depth--;
                }
            }
        }

        // If we have unclosed brackets, try to close them
        if ($depth > 0) {
            Log::info('Attempting to repair JSON', ['unclosed_brackets' => $depth]);

            // Remove any trailing incomplete JSON elements
            $content = rtrim($content);

            // Remove trailing commas
            if (substr($content, -1) === ',') {
                $content = substr($content, 0, -1);
            }

            // Add missing closing brackets
            $closingBrackets = str_repeat($endChar, $depth);

            return $content.$closingBrackets;
        }

        return false;
    }

    /**
     * Find matching closing bracket for a given opening bracket
     */
    protected function findMatchingBracket(string $content, int $start, string $openChar, string $closeChar): int|false
    {
        $depth = 0;
        $length = strlen($content);

        for ($i = $start; $i < $length; $i++) {
            $char = $content[$i];

            if ($char === $openChar) {
                $depth++;
            } elseif ($char === $closeChar) {
                $depth--;

                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return false;
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
