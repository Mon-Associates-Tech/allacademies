<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicChatSession;
use App\Models\Book;
use App\Models\QuizSession;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Services\ResearchAssistantService;
use App\Services\BookBasedLearningService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Extended Student Dashboard Controller with Book-Based Learning Features
 */
class StudentDashboardController extends Controller
{
    protected $chatService;

    protected $bookLearningService;

    public function __construct(
        ResearchAssistantService $chatService,
        BookBasedLearningService $bookLearningService
    ) {
        $this->chatService = $chatService;
        $this->bookLearningService = $bookLearningService;
        $this->middleware('auth');
    }

    /**
     * Enhanced dashboard with book reading features
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Get student's learning profile
        $learningProfile = $this->buildLearningProfile($user);

        // Get recent chat sessions
        $recentSessions = AcademicChatSession::where('user_id', $user->id)
            ->latest('last_activity')
            ->take(5)
            ->get();

        // Get currently reading books
        $currentlyReading = $this->getCurrentlyReadingBooks($user);

        // Get reading progress
        $readingProgress = $this->getReadingProgress($user);

        // Get recommended books
        $recommendedBooks = $this->getRecommendedBooks($user);

        // Get recent quiz results
        $recentQuizzes = $this->getRecentQuizResults($user);

        // Get learning insights
        $insights = $this->chatService->getLearningInsights($user->id);

        return view('student.dashboard', [
            'learningProfile' => $learningProfile,
            'recentSessions' => $recentSessions,
            'currentlyReading' => $currentlyReading,
            'readingProgress' => $readingProgress,
            'recommendedBooks' => $recommendedBooks,
            'recentQuizzes' => $recentQuizzes,
            'insights' => $insights,
            'availableSubjects' => $this->chatService->getAvailableSubjects(),
        ]);
    }

    /**
     * Enhanced learning profile builder using existing models
     */
    protected function buildLearningProfile(User $user): array
    {
        $readingProgress = $this->getReadingProgress($user);
        $recentQuizzes = $this->getRecentQuizResults($user);

        return [
            'basic_info' => [
                'age' => $user->age,
                'academic_level' => $user->academic_level ?? 'high_school',
                'learning_style' => $user->learning_style ?? 'visual',
            ],
            'reading_stats' => $readingProgress,
            'quiz_performance' => [
                'recent_quizzes' => $recentQuizzes->count(),
                'average_score' => $recentQuizzes->avg('score') ?? 0,
                'best_score' => $recentQuizzes->max('score') ?? 0,
                'total_quizzes' => QuizSession::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
            ],
            'achievements' => ReadingAchievement::where('user_id', $user->id)
                ->latest('earned_at')
                ->limit(5)
                ->get()
                ->map(function ($achievement) {
                    return [
                        'name' => $achievement->achievement_name,
                        'description' => $achievement->description,
                        'earned_at' => $achievement->earned_at->diffForHumans(),
                    ];
                }),
            'goals' => [
                'current_streak' => $readingProgress['reading_streak'],
                'books_this_month' => $this->getBooksReadThisMonth($user),
                'pages_this_week' => $this->getPagesReadThisWeek($user),
            ],
        ];
    }

    /**
     * Get reading progress summary using existing models
     */
    protected function getReadingProgress(User $user): array
    {
        $allProgress = BookReadingProgress::where('user_id', $user->id)->get();
        $completed = $allProgress->filter(function ($progress) {
            return $progress->current_page >= $progress->total_pages;
        });
        $currentlyReading = $allProgress->filter(function ($progress) {
            return $progress->current_page < $progress->total_pages;
        });

        return [
            'total_books_started' => $allProgress->count(),
            'books_completed' => $completed->count(),
            'currently_reading' => $currentlyReading->count(),
            'total_pages_read' => $allProgress->sum('current_page'),
            'average_progress' => $allProgress->count() > 0 ?
                $allProgress->avg(function ($progress) {
                    return ($progress->current_page / max($progress->total_pages, 1)) * 100;
                }) : 0,
            'reading_streak' => $this->calculateReadingStreak($user),
            'favorite_categories' => $this->getFavoriteCategories($allProgress),
        ];
    }

    /**
     * Calculate reading streak using existing model
     */
    protected function calculateReadingStreak(User $user): int
    {
        $recentActivity = BookReadingProgress::where('user_id', $user->id)
            ->whereNotNull('last_read_at')
            ->where('last_read_at', '>=', now()->subDays(30))
            ->orderBy('last_read_at', 'desc')
            ->pluck('last_read_at')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            })
            ->unique()
            ->values();

        if ($recentActivity->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = now()->format('Y-m-d');

        // Check if user read today or yesterday
        if (! $recentActivity->contains($currentDate) &&
            ! $recentActivity->contains(now()->subDay()->format('Y-m-d'))) {
            return 0;
        }

        // Count consecutive days
        for ($i = 0; $i < $recentActivity->count(); $i++) {
            $expectedDate = now()->subDays($i)->format('Y-m-d');
            if ($recentActivity->contains($expectedDate)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get favorite categories from reading history
     */
    protected function getFavoriteCategories($readingProgress): array
    {
        $categories = [];

        foreach ($readingProgress as $progress) {
            if ($progress->book && $progress->book->bookCategory) {
                $categoryName = $progress->book->bookCategory->name;
                $categories[$categoryName] = ($categories[$categoryName] ?? 0) + 1;
            }
        }

        arsort($categories);

        return array_slice($categories, 0, 3, true);
    }

    /**
     * Get recent quiz results using existing models
     */
    protected function getRecentQuizResults(User $user)
    {
        return QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('book.author')
            ->latest('completed_at')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                $results = $session->results ?? [];

                return [
                    'id' => $session->id,
                    'book_title' => $session->book->title,
                    'author' => $session->book->author->name ?? 'Unknown Author',
                    'score' => $results['percentage'] ?? 0,
                    'grade' => $this->calculateLetterGrade($results['percentage'] ?? 0),
                    'question_count' => $results['total_questions'] ?? 0,
                    'date' => $session->completed_at,
                    'time_taken' => $session->time_taken,
                ];
            });
    }

    /**
     * Get books read this month
     */
    protected function getBooksReadThisMonth(User $user): int
    {
        return BookReadingProgress::where('user_id', $user->id)
            ->whereColumn('current_page', '>=', 'total_pages')
            ->whereMonth('last_read_at', now()->month)
            ->whereYear('last_read_at', now()->year)
            ->count();
    }

    /**
     * Get pages read this week
     */
    protected function getPagesReadThisWeek(User $user): int
    {
        return BookReadingProgress::where('user_id', $user->id)
            ->where('last_read_at', '>=', now()->subWeek())
            ->sum('current_page');
    }

    /**
     * Get currently reading books using existing BookReadingProgress model
     */
    protected function getCurrentlyReadingBooks(User $user)
    {
        return BookReadingProgress::where('user_id', $user->id)
            ->whereColumn('current_page', '<', 'total_pages')
            ->with(['book.author', 'book.bookCategory'])
            ->latest('last_read_at')
            ->get();
    }

    /**
     * Get recommended books using existing models
     */
    protected function getRecommendedBooks(User $user)
    {
        // Get user's reading history to determine preferences
        $readingHistory = BookReadingProgress::where('user_id', $user->id)
            ->with('book.bookCategory')
            ->get();

        $favoriteCategories = $this->getFavoriteCategories($readingHistory);
        $completedBookIds = $readingHistory->pluck('book_id')->toArray();

        $query = Book::published()
            ->with(['author', 'bookCategory'])
            ->whereNotIn('id', $completedBookIds);

        // Prioritize books in favorite categories
        if (! empty($favoriteCategories)) {
            $query->whereHas('bookCategory', function ($q) use ($favoriteCategories) {
                $q->whereIn('name', array_keys($favoriteCategories));
            });
        }

        return $query->inRandomOrder()->limit(6)->get();
    }

    /**
     * Get available books for the user's academic level and subjects
     */
    public function getAvailableBooks(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'subject' => 'nullable|string',
            'academic_level' => 'nullable|string',
            'author' => 'nullable|string',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Book::query()
            ->where('is_active', true)
            ->whereJsonContains('academic_levels', $user->academic_level);

        if ($validated['subject'] ?? null) {
            $query->whereJsonContains('subjects', $validated['subject']);
        }

        if ($validated['academic_level'] ?? null) {
            $query->whereJsonContains('academic_levels', $validated['academic_level']);
        }

        if ($validated['author'] ?? null) {
            $query->where('author', 'LIKE', '%'.$validated['author'].'%');
        }

        if ($validated['search'] ?? null) {
            $query->where(function ($q) use ($validated) {
                $q->where('title', 'LIKE', '%'.$validated['search'].'%')
                    ->orWhere('author', 'LIKE', '%'.$validated['search'].'%')
                    ->orWhere('description', 'LIKE', '%'.$validated['search'].'%');
            });
        }

        $books = $query->with(['chapters'])
            ->paginate(20);

        return response()->json([
            'success' => true,
            'books' => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'total_pages' => $books->lastPage(),
                'total_items' => $books->total(),
            ],
        ]);
    }

    /**
     * Start reading a book - track reading progress
     */
    public function startReading(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'chapter_id' => 'nullable|exists:book_chapters,id',
            'page_number' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();
        $book = Book::findOrFail($validated['book_id']);

        // Create or update reading progress
        $progress = ReadingProgress::updateOrCreate([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ], [
            'current_chapter_id' => $validated['chapter_id'] ?? $book->chapters->first()?->id,
            'current_page' => $validated['page_number'] ?? 1,
            'status' => 'reading',
            'started_at' => now(),
            'last_read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Started reading '{$book->title}'",
            'reading_progress' => $progress,
            'book_details' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'total_chapters' => $book->chapters->count(),
                'total_pages' => $book->total_pages,
                'current_chapter' => $progress->currentChapter?->title,
                'current_page' => $progress->current_page,
            ],
        ]);
    }

    /**
     * Quiz me - Generate questions based on book content
     */
    public function quizMe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'chapter_id' => 'nullable|exists:book_chapters,id',
            'page_start' => 'nullable|integer|min:1',
            'page_end' => 'nullable|integer|min:1',
            'question_type' => 'required|in:essay,multiple_choice,true_false,mixed',
            'question_count' => 'required|integer|min:1|max:20',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
            'focus_topics' => 'nullable|array',
            'include_quotes' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $book = Book::with(['chapters', 'sections'])->findOrFail($validated['book_id']);

        try {
            // Get reading context
            $readingContext = $this->buildReadingContext($book, $validated, $user);

            // Generate quiz questions using AI
            $quizData = $this->generateBookBasedQuiz($readingContext, $validated, $user);

            // Create quiz session
            $quizSession = QuizSession::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'chapter_id' => $validated['chapter_id'] ?? null,
                'page_start' => $validated['page_start'] ?? null,
                'page_end' => $validated['page_end'] ?? null,
                'question_type' => $validated['question_type'],
                'question_count' => $validated['question_count'],
                'difficulty' => $validated['difficulty'] ?? 'medium',
                'questions' => $quizData['questions'],
                'context' => $readingContext,
                'status' => 'active',
                'started_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'quiz_session' => [
                    'id' => $quizSession->id,
                    'book_title' => $book->title,
                    'author' => $book->author->name ?? 'Unknown Author',
                    'context' => $readingContext['display'],
                    'question_type' => $validated['question_type'],
                    'total_questions' => count($quizData['questions']),
                    'estimated_duration' => $this->estimateQuizDuration($quizData['questions']),
                    'difficulty' => $validated['difficulty'] ?? 'medium',
                ],
                'questions' => $this->formatQuestionsForFrontend($quizData['questions'], $validated['question_type']),
                'instructions' => $this->getQuizInstructions($validated['question_type'], $book),
                'reading_tips' => $this->getReadingTips($user->learning_style ?? 'visual', $book->bookCategory->name ?? 'General'),
            ]);

        } catch (Exception $e) {
            Log::error('Quiz generation failed', [
                'user_id' => $user->id,
                'book_id' => $book->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to generate quiz. Please try again or select a different range.',
                'fallback_options' => $this->getFallbackQuizOptions($book),
            ], 500);
        }
    }

    /**
     * Build comprehensive reading context for quiz generation
     */
    protected function buildReadingContext(Book $book, array $parameters, User $user): array
    {
        $context = [
            'book' => [
                'title' => $book->title,
                'author' => $book->author,
                'genre' => $book->genre,
                'publication_year' => $book->publication_year,
                'summary' => $book->summary,
                'themes' => $book->themes ?? [],
                'reading_level' => $book->reading_level,
            ],
            'scope' => [],
            'user_context' => [
                'academic_level' => $user->academic_level,
                'age' => $user->age,
                'learning_style' => $user->learning_style,
                'reading_preferences' => $user->reading_preferences ?? [],
            ],
        ];

        // Add chapter context if specified
        if ($parameters['chapter_id'] ?? null) {
            $chapter = $book->chapters()->find($parameters['chapter_id']);
            if ($chapter) {
                $context['scope']['chapter'] = [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'summary' => $chapter->summary,
                    'key_concepts' => $chapter->key_concepts ?? [],
                    'page_start' => $chapter->page_start,
                    'page_end' => $chapter->page_end,
                ];
            }
        }

        // Add page range context
        if (($parameters['page_start'] ?? null) && ($parameters['page_end'] ?? null)) {
            $context['scope']['pages'] = [
                'start' => $parameters['page_start'],
                'end' => $parameters['page_end'],
                'range' => $parameters['page_end'] - $parameters['page_start'] + 1,
            ];
        }

        // Add display context for frontend
        $context['display'] = $this->buildDisplayContext($context);

        return $context;
    }

    /**
     * Generate quiz questions using AI based on book content
     */
    protected function generateBookBasedQuiz(array $context, array $parameters, User $user): array
    {
        $quizPrompt = $this->buildQuizPrompt($context, $parameters);

        $chatParameters = [
            'message' => $quizPrompt,
            'age' => $user->age,
            'academic_level' => $user->academic_level,
            'subject' => 'language_arts',
            'topics' => ['reading_comprehension', 'literary_analysis'],
            'learning_style' => $user->learning_style,
            'response_format' => 'structured',
            'difficulty' => $parameters['difficulty'] ?? 'medium',
            'creativity_level' => 0.6, // Balanced creativity for questions
            'response_length' => 2000,
        ];

        $result = $this->chatService->chat($chatParameters);

        if (! $result['success']) {
            throw new Exception('Failed to generate quiz questions: '.$result['error']);
        }

        // Parse the AI response into structured questions
        return $this->parseQuizResponse($result['content'], $parameters['question_type'], $parameters['question_count']);
    }

    /**
     * Build quiz generation prompt
     */
    protected function buildQuizPrompt(array $context, array $parameters): string
    {
        $book = $context['book'];
        $questionCount = $parameters['question_count'];
        $questionType = $parameters['question_type'];
        $difficulty = $parameters['difficulty'] ?? 'medium';

        $prompt = "Generate {$questionCount} {$difficulty} level {$questionType} questions based on:\n\n";
        $prompt .= "Book: \"{$book['title']}\" by {$book['author']}\n";
        $prompt .= "Genre: {$book['genre']}\n";

        if (isset($context['scope']['chapter'])) {
            $chapter = $context['scope']['chapter'];
            $prompt .= "Chapter: \"{$chapter['title']}\"\n";
            if ($chapter['summary']) {
                $prompt .= "Chapter Summary: {$chapter['summary']}\n";
            }
        }

        if (isset($context['scope']['pages'])) {
            $pages = $context['scope']['pages'];
            $prompt .= "Page Range: {$pages['start']} - {$pages['end']}\n";
        }

        if ($book['themes']) {
            $prompt .= 'Key Themes: '.implode(', ', $book['themes'])."\n";
        }

        $prompt .= "\nQuestion Guidelines:\n";

        switch ($questionType) {
            case 'multiple_choice':
                $prompt .= "- Create multiple choice questions with 4 options (A, B, C, D)\n";
                $prompt .= "- Mark the correct answer clearly\n";
                $prompt .= "- Ensure distractors are plausible but clearly incorrect\n";
                $prompt .= "- Focus on comprehension, analysis, and interpretation\n";
                break;

            case 'true_false':
                $prompt .= "- Create true/false statements\n";
                $prompt .= "- Include both factual and analytical questions\n";
                $prompt .= "- Provide brief explanations for answers\n";
                break;

            case 'essay':
                $prompt .= "- Create essay questions requiring 150-300 word responses\n";
                $prompt .= "- Focus on analysis, interpretation, and critical thinking\n";
                $prompt .= "- Provide key points that should be addressed\n";
                $prompt .= "- Include evaluation rubric criteria\n";
                break;

            case 'mixed':
                $distribution = $this->getMixedQuestionDistribution($questionCount);
                $prompt .= "- Create a mix of question types:\n";
                $prompt .= "  * {$distribution['multiple_choice']} multiple choice questions\n";
                $prompt .= "  * {$distribution['true_false']} true/false questions\n";
                $prompt .= "  * {$distribution['essay']} essay questions\n";
                break;
        }

        $prompt .= "\nDifficulty Level ({$difficulty}):\n";
        switch ($difficulty) {
            case 'easy':
                $prompt .= "- Focus on basic comprehension and recall\n";
                $prompt .= "- Test understanding of main events and characters\n";
                break;
            case 'medium':
                $prompt .= "- Include analysis and interpretation questions\n";
                $prompt .= "- Test understanding of themes and character development\n";
                break;
            case 'hard':
                $prompt .= "- Require critical thinking and deep analysis\n";
                $prompt .= "- Include questions about literary devices and complex themes\n";
                break;
        }

        if ($parameters['focus_topics'] ?? null) {
            $prompt .= "\nFocus on these specific topics: ".implode(', ', $parameters['focus_topics'])."\n";
        }

        if ($parameters['include_quotes'] ?? false) {
            $prompt .= "\nInclude questions that reference specific quotes from the text when appropriate.\n";
        }

        $prompt .= "\nFormat your response as structured JSON with questions, options (if applicable), correct answers, and explanations.";

        return $prompt;
    }

    /**
     * Parse AI response into structured quiz data
     */
    protected function parseQuizResponse(string $response, string $questionType, int $questionCount): array
    {
        // Try to extract JSON from the response
        $jsonMatch = [];
        if (preg_match('/\{.*\}/s', $response, $jsonMatch)) {
            $jsonData = json_decode($jsonMatch[0], true);
            if ($jsonData && isset($jsonData['questions'])) {
                return [
                    'questions' => $jsonData['questions'],
                    'metadata' => $jsonData['metadata'] ?? [],
                ];
            }
        }

        // Fallback: Parse structured text response
        return $this->parseTextQuizResponse($response, $questionType, $questionCount);
    }

    /**
     * Parse text-based quiz response (fallback method)
     */
    protected function parseTextQuizResponse(string $response, string $questionType, int $questionCount): array
    {
        $questions = [];
        $lines = explode("\n", $response);
        $currentQuestion = null;
        $questionIndex = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Detect question start
            if (preg_match('/^(\d+[\.\)]|\*\*Question \d+|\d+:)/', $line)) {
                if ($currentQuestion) {
                    $questions[] = $this->finalizeQuestion($currentQuestion, $questionType);
                }

                $currentQuestion = [
                    'id' => ++$questionIndex,
                    'question' => preg_replace('/^(\d+[\.\)]|\*\*Question \d+\*\*:?|\d+:)\s*/', '', $line),
                    'type' => $questionType === 'mixed' ? $this->detectQuestionType($line) : $questionType,
                    'options' => [],
                    'correct_answer' => null,
                    'explanation' => null,
                    'difficulty' => 'medium',
                ];
            } // Detect multiple choice options
            elseif ($currentQuestion && preg_match('/^[A-D][\.\)]/', $line)) {
                $currentQuestion['options'][] = [
                    'id' => substr($line, 0, 1),
                    'text' => trim(substr($line, 2)),
                ];
            } // Detect answers
            elseif ($currentQuestion && (stripos($line, 'answer:') === 0 || stripos($line, 'correct:') === 0)) {
                $currentQuestion['correct_answer'] = trim(str_ireplace(['answer:', 'correct:'], '', $line));
            } // Detect explanations
            elseif ($currentQuestion && (stripos($line, 'explanation:') === 0 || stripos($line, 'rationale:') === 0)) {
                $currentQuestion['explanation'] = trim(str_ireplace(['explanation:', 'rationale:'], '', $line));
            }
        }

        // Add the last question
        if ($currentQuestion) {
            $questions[] = $this->finalizeQuestion($currentQuestion, $questionType);
        }

        // Ensure we have the requested number of questions
        $questions = array_slice($questions, 0, $questionCount);

        return [
            'questions' => $questions,
            'metadata' => [
                'generated_count' => count($questions),
                'requested_count' => $questionCount,
                'question_type' => $questionType,
            ],
        ];
    }

    /**
     * Finalize question formatting based on type
     */
    protected function finalizeQuestion(array $question, string $questionType): array
    {
        switch ($question['type']) {
            case 'multiple_choice':
                if (empty($question['options'])) {
                    // Generate default options if none provided
                    $question['options'] = [
                        ['id' => 'A', 'text' => 'Option A'],
                        ['id' => 'B', 'text' => 'Option B'],
                        ['id' => 'C', 'text' => 'Option C'],
                        ['id' => 'D', 'text' => 'Option D'],
                    ];
                    $question['correct_answer'] = 'A';
                }
                break;

            case 'true_false':
                $question['options'] = [
                    ['id' => 'true', 'text' => 'True'],
                    ['id' => 'false', 'text' => 'False'],
                ];
                if (! in_array(strtolower($question['correct_answer'] ?? ''), ['true', 'false'])) {
                    $question['correct_answer'] = 'true';
                }
                break;

            case 'essay':
                $question['options'] = [];
                $question['rubric'] = $this->generateEssayRubric($question);
                break;
        }

        return $question;
    }

    /**
     * Get fallback quiz options when generation fails
     */
    protected function getFallbackQuizOptions(Book $book): array
    {
        return [
            'suggestions' => [
                'Try selecting a specific chapter instead of the entire book',
                'Use a smaller page range (20-50 pages works best)',
                'Choose "Easy" or "Medium" difficulty',
                'Reduce the number of questions to 5-10',
            ],
            'alternative_books' => Book::where('book_category_id', $book->book_category_id)
                ->where('id', '!=', $book->id)
                ->published()
                ->limit(3)
                ->pluck('title', 'id')
                ->toArray(),
        ];
    }

    /**
     * Ask questions about specific book content
     */
    public function askBookQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'question' => 'required|string|max:1000',
            'chapter_id' => 'nullable|exists:book_chapters,id',
            'section_id' => 'nullable|exists:book_sections,id',
            'page_number' => 'nullable|integer|min:1',
            'quote' => 'nullable|string|max:500',
            'question_type' => 'nullable|in:comprehension,analysis,interpretation,character,theme,plot,setting',
        ]);

        $user = auth()->user();
        $book = Book::with(['chapters', 'sections'])->findOrFail($validated['book_id']);

        // Build comprehensive context for the book question
        $questionContext = $this->buildBookQuestionContext($book, $validated, $user);

        // Prepare AI parameters for book-specific questioning
        $chatParameters = [
            'message' => $this->formatBookQuestion($validated, $questionContext),
            'age' => $user->age,
            'academic_level' => $user->academic_level,
            'subject' => $this->determineBookSubject($book),
            'topics' => $this->extractBookTopics($book, $validated),
            'learning_style' => $user->learning_style,
            'difficulty' => $this->determineQuestionDifficulty($validated['question'], $book),
            'response_format' => 'detailed',
            'accommodations' => $user->learning_accommodations ?? [],
            'creativity_level' => 0.7, // Balanced for literary analysis
            'response_length' => 1200,
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            // Log the book-based question for analytics
            $this->logBookQuestion($user->id, $book->id, $validated, $result);

            return response()->json([
                'success' => true,
                'response' => $result['content'],
                'context' => [
                    'book' => $book->title,
                    'author' => $book->author,
                    'chapter' => $questionContext['chapter_title'] ?? null,
                    'section' => $questionContext['section_title'] ?? null,
                    'page' => $validated['page_number'] ?? null,
                ],
                'follow_up_suggestions' => $this->generateBookFollowUpQuestions($book, $validated, $result),
                'related_concepts' => $this->getRelatedLiteraryConcepts($book, $validated),
                'reading_comprehension_tips' => $this->getComprehensionTips($user->learning_style, $book->genre),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Unable to process your question about the book. Please try rephrasing or contact support.',
        ], 500);
    }

    /**
     * Submit quiz answers and get results
     */
    public function submitQuizAnswers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quiz_session_id' => 'required|exists:quiz_sessions,id',
            'answers' => 'required|array',
            'time_taken' => 'nullable|integer|min:1', // in seconds
        ]);

        $user = auth()->user();
        $quizSession = QuizSession::where('id', $validated['quiz_session_id'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        try {
            // Grade the quiz
            $gradingResult = $this->gradeQuiz($quizSession, $validated['answers']);

            // Update quiz session
            $quizSession->update([
                'answers' => $validated['answers'],
                'results' => $gradingResult,
                'time_taken' => $validated['time_taken'] ?? null,
                'completed_at' => now(),
                'status' => 'completed',
            ]);

            // Update reading progress based on quiz performance
            $this->updateReadingProgressFromQuiz($user, $quizSession, $gradingResult);

            // Generate personalized feedback
            $feedback = $this->generateQuizFeedback($quizSession, $gradingResult, $user);

            return response()->json([
                'success' => true,
                'results' => [
                    'score' => $gradingResult['score'],
                    'percentage' => $gradingResult['percentage'],
                    'total_questions' => $gradingResult['total_questions'],
                    'correct_answers' => $gradingResult['correct_answers'],
                    'grade' => $this->calculateLetterGrade($gradingResult['percentage']),
                    'time_taken' => $validated['time_taken'] ?? null,
                    'performance_level' => $this->getPerformanceLevel($gradingResult['percentage']),
                ],
                'detailed_feedback' => $feedback,
                'question_breakdown' => $gradingResult['question_details'],
                'improvement_suggestions' => $this->getImprovementSuggestions($gradingResult, $quizSession),
                'next_steps' => $this->getNextReadingSteps($user, $quizSession->book, $gradingResult),
                'badges_earned' => $this->checkBadgesEarned($user, $gradingResult, $quizSession),
            ]);

        } catch (Exception $e) {
            Log::error('Quiz grading failed', [
                'quiz_session_id' => $quizSession->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to grade quiz. Please try again.',
            ], 500);
        }
    }

    /**
     * Get book analysis and insights
     */
    public function getBookAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'analysis_type' => 'required|in:themes,characters,plot,setting,style,symbolism,historical_context',
            'chapter_id' => 'nullable|exists:book_chapters,id',
            'depth' => 'nullable|in:overview,detailed,comprehensive',
        ]);

        $user = auth()->user();
        $book = Book::with(['chapters', 'sections'])->findOrFail($validated['book_id']);

        // Build analysis context
        $analysisContext = $this->buildAnalysisContext($book, $validated);

        // Generate AI-powered analysis
        $analysisPrompt = $this->buildAnalysisPrompt($book, $validated, $analysisContext);

        $chatParameters = [
            'message' => $analysisPrompt,
            'age' => $user->age,
            'academic_level' => $user->academic_level,
            'subject' => 'language_arts',
            'topics' => ['literary_analysis', $validated['analysis_type']],
            'learning_style' => $user->learning_style,
            'response_format' => 'detailed',
            'difficulty' => $this->getAnalysisDifficulty($user->academic_level),
            'creativity_level' => 0.8, // Higher creativity for analysis
            'response_length' => 1500,
        ];

        $result = $this->chatService->chat($chatParameters);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'analysis' => [
                    'type' => $validated['analysis_type'],
                    'book' => $book->title,
                    'author' => $book->author,
                    'content' => $result['content'],
                    'depth_level' => $validated['depth'] ?? 'detailed',
                ],
                'discussion_questions' => $this->generateDiscussionQuestions($book, $validated['analysis_type']),
                'related_readings' => $this->getRelatedReadings($book, $validated['analysis_type']),
                'study_activities' => $this->getSuggestedStudyActivities($book, $validated['analysis_type'], $user->learning_style),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Unable to generate analysis. Please try again.',
        ], 500);
    }

    /**
     * Get reading recommendations based on current books and preferences
     */
    public function getReadingRecommendations(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Get user's reading history and preferences
        $readingHistory = $this->getUserReadingHistory($user);
        $preferences = $this->getUserReadingPreferences($user);

        // Get AI-powered recommendations
        $recommendations = $this->generateReadingRecommendations($user, $readingHistory, $preferences);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'based_on' => [
                'reading_history' => count($readingHistory),
                'preferred_genres' => $preferences['genres'] ?? [],
                'academic_level' => $user->academic_level,
                'recent_performance' => $this->getRecentReadingPerformance($user),
            ],
        ]);
    }

    /**
     * Build reading context using existing Book model structure
     */
    protected function buildReadingContextFromExistingModel(Book $book, array $parameters, User $user): array
    {
        $context = [
            'book' => [
                'title' => $book->title,
                'author' => $book->author->name ?? 'Unknown Author',
                'category' => $book->bookCategory->name ?? 'General',
                'pages' => $book->pages,
                'summary' => $book->additional_info ?? '',
                'average_rating' => $book->average_rating,
                'has_audio' => $book->has_audio,
                'has_video' => $book->has_video,
            ],
            'scope' => [],
            'user_context' => [
                'academic_level' => $user->academic_level ?? 'high_school',
                'age' => $user->age,
                'learning_style' => $user->learning_style ?? 'visual',
                'reading_preferences' => $user->reading_preferences ?? [],
            ],
        ];

        // Add chapter context if specified using existing table_of_contents
        if ($parameters['chapter_number'] ?? null) {
            $chapterNumber = $parameters['chapter_number'];
            $tableOfContents = $book->formatted_table_of_contents ?? [];

            $chapter = collect($tableOfContents)->firstWhere('chapter_number', $chapterNumber);
            if ($chapter) {
                $context['scope']['chapter'] = [
                    'number' => $chapter['chapter_number'],
                    'title' => $chapter['title'],
                    'description' => $chapter['description'] ?? '',
                    'page_range' => $chapter['page_range'] ?? '',
                    'page_count' => $chapter['page_count'] ?? 0,
                ];
            }
        }

        // Add page range context
        if (($parameters['page_start'] ?? null) && ($parameters['page_end'] ?? null)) {
            $context['scope']['pages'] = [
                'start' => $parameters['page_start'],
                'end' => $parameters['page_end'],
                'range' => $parameters['page_end'] - $parameters['page_start'] + 1,
            ];
        }

        // Add display context for frontend
        $context['display'] = $this->buildDisplayContextFromExisting($context, $book);

        return $context;
    }

    /**
     * Build display context for frontend using existing model data
     */
    protected function buildDisplayContextFromExisting(array $context, Book $book): string
    {
        $parts = [$book->title];

        if (isset($context['scope']['chapter'])) {
            $chapter = $context['scope']['chapter'];
            $parts[] = "Chapter {$chapter['number']}: {$chapter['title']}";
        }

        if (isset($context['scope']['pages'])) {
            $pages = $context['scope']['pages'];
            $parts[] = "Pages {$pages['start']}-{$pages['end']}";
        }

        return implode(' | ', $parts);
    }

    // ========================
    // PROTECTED HELPER METHODS
    // ========================

    /**
     * Generate quiz using existing model structure
     */
    protected function generateBookBasedQuizFromExistingModel(array $context, array $parameters, User $user): array
    {
        $quizPrompt = $this->buildQuizPromptFromExistingModel($context, $parameters);

        $chatParameters = [
            'message' => $quizPrompt,
            'age' => $user->age,
            'academic_level' => $user->academic_level ?? 'high_school',
            'subject' => 'language_arts',
            'topics' => ['reading_comprehension', 'literary_analysis'],
            'learning_style' => $user->learning_style ?? 'visual',
            'response_format' => 'structured',
            'difficulty' => $parameters['difficulty'] ?? 'medium',
            'creativity_level' => 0.6,
            'response_length' => 2000,
        ];

        $result = $this->chatService->chat($chatParameters);

        if (! $result['success']) {
            throw new Exception('Failed to generate quiz questions: '.$result['error']);
        }

        // Parse the AI response into structured questions
        return $this->parseQuizResponseFromExisting($result['content'], $parameters['question_type'], $parameters['question_count']);
    }

    /**
     * Build quiz prompt using existing model data
     */
    protected function buildQuizPromptFromExistingModel(array $context, array $parameters): string
    {
        $book = $context['book'];
        $questionCount = $parameters['question_count'];
        $questionType = $parameters['question_type'];
        $difficulty = $parameters['difficulty'] ?? 'medium';

        $prompt = "Generate {$questionCount} {$difficulty} level {$questionType} questions based on:\n\n";
        $prompt .= "Book: \"{$book['title']}\" by {$book['author']}\n";
        $prompt .= "Category: {$book['category']}\n";
        $prompt .= "Total Pages: {$book['pages']}\n";

        if ($book['summary']) {
            $prompt .= "Book Summary: {$book['summary']}\n";
        }

        if (isset($context['scope']['chapter'])) {
            $chapter = $context['scope']['chapter'];
            $prompt .= "Chapter: {$chapter['number']} - \"{$chapter['title']}\"\n";
            if ($chapter['description']) {
                $prompt .= "Chapter Description: {$chapter['description']}\n";
            }
        }

        if (isset($context['scope']['pages'])) {
            $pages = $context['scope']['pages'];
            $prompt .= "Page Range: {$pages['start']} - {$pages['end']}\n";
        }

        $prompt .= "\nQuestion Guidelines:\n";

        switch ($questionType) {
            case 'multiple_choice':
                $prompt .= "- Create multiple choice questions with 4 options (A, B, C, D)\n";
                $prompt .= "- Mark the correct answer clearly\n";
                $prompt .= "- Ensure distractors are plausible but clearly incorrect\n";
                $prompt .= "- Focus on comprehension, analysis, and interpretation\n";
                break;

            case 'true_false':
                $prompt .= "- Create true/false statements about the book content\n";
                $prompt .= "- Include both factual and analytical questions\n";
                $prompt .= "- Provide brief explanations for answers\n";
                break;

            case 'essay':
                $prompt .= "- Create essay questions requiring 150-300 word responses\n";
                $prompt .= "- Focus on analysis, interpretation, and critical thinking\n";
                $prompt .= "- Provide key points that should be addressed\n";
                $prompt .= "- Include evaluation rubric criteria\n";
                break;

            case 'mixed':
                $distribution = $this->getMixedQuestionDistribution($questionCount);
                $prompt .= "- Create a mix of question types:\n";
                $prompt .= "  * {$distribution['multiple_choice']} multiple choice questions\n";
                $prompt .= "  * {$distribution['true_false']} true/false questions\n";
                $prompt .= "  * {$distribution['essay']} essay questions\n";
                break;
        }

        $prompt .= "\nDifficulty Level ({$difficulty}):\n";
        switch ($difficulty) {
            case 'easy':
                $prompt .= "- Focus on basic comprehension and recall\n";
                $prompt .= "- Test understanding of main events and characters\n";
                break;
            case 'medium':
                $prompt .= "- Include analysis and interpretation questions\n";
                $prompt .= "- Test understanding of themes and character development\n";
                break;
            case 'hard':
                $prompt .= "- Require critical thinking and deep analysis\n";
                $prompt .= "- Include questions about literary devices and complex themes\n";
                break;
        }

        if ($parameters['focus_topics'] ?? null) {
            $prompt .= "\nFocus on these specific topics: ".implode(', ', $parameters['focus_topics'])."\n";
        }

        if ($parameters['include_quotes'] ?? false) {
            $prompt .= "\nInclude questions that reference specific quotes from the text when appropriate.\n";
        }

        $prompt .= "\nFormat your response as structured JSON with questions, options (if applicable), correct answers, and explanations.";

        return $prompt;
    }

    /**
     * Parse quiz response adapted for existing model structure
     */
    protected function parseQuizResponseFromExisting(string $response, string $questionType, int $questionCount): array
    {
        // Try to extract JSON from the response
        $jsonMatch = [];
        if (preg_match('/\{.*\}/s', $response, $jsonMatch)) {
            $jsonData = json_decode($jsonMatch[0], true);
            if ($jsonData && isset($jsonData['questions'])) {
                return [
                    'questions' => $this->validateQuestions($jsonData['questions']),
                    'metadata' => $jsonData['metadata'] ?? [],
                ];
            }
        }

        // Fallback: Parse structured text response
        return $this->parseTextQuizResponseFromExisting($response, $questionType, $questionCount);
    }

    /**
     * Validate and format questions
     */
    protected function validateQuestions(array $questions): array
    {
        return array_map(function ($question, $index) {
            return [
                'id' => $question['id'] ?? ($index + 1),
                'question' => $question['question'] ?? 'Question text missing',
                'type' => $question['type'] ?? 'multiple_choice',
                'options' => $question['options'] ?? [],
                'correct_answer' => $question['correct_answer'] ?? '',
                'explanation' => $question['explanation'] ?? '',
                'difficulty' => $question['difficulty'] ?? 'medium',
                'points' => $this->getQuestionPoints($question['type'] ?? 'multiple_choice'),
            ];
        }, $questions, array_keys($questions));
    }

    /**
     * Additional helper methods for existing model compatibility
     */
    protected function parseTextQuizResponseFromExisting(string $response, string $questionType, int $questionCount): array
    {
        // Basic text parsing implementation for fallback
        $questions = [];
        $lines = explode("\n", $response);
        $currentQuestion = null;
        $questionIndex = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Detect question start
            if (preg_match('/^(\d+[\.\)]|\*\*Question \d+)/', $line)) {
                if ($currentQuestion) {
                    $questions[] = $this->finalizeTextQuestion($currentQuestion, $questionType);
                }

                $questionIndex++;
                $currentQuestion = [
                    'id' => $questionIndex,
                    'question' => preg_replace('/^(\d+[\.\)]|\*\*Question \d+\*\*:?)\s*/', '', $line),
                    'type' => $questionType === 'mixed' ? $this->detectQuestionType($line) : $questionType,
                    'options' => [],
                    'correct_answer' => '',
                    'explanation' => '',
                    'difficulty' => 'medium',
                    'points' => $this->getQuestionPoints($questionType),
                ];
            }
        }

        if ($currentQuestion) {
            $questions[] = $this->finalizeTextQuestion($currentQuestion, $questionType);
        }

        return [
            'questions' => array_slice($questions, 0, $questionCount),
            'metadata' => [
                'generated_count' => count($questions),
                'parsing_method' => 'text_fallback',
            ],
        ];
    }

    /**
     * Finalize text question with proper structure
     */
    protected function finalizeTextQuestion(array $question, string $questionType): array
    {
        switch ($question['type']) {
            case 'multiple_choice':
                if (empty($question['options'])) {
                    $question['options'] = [
                        ['id' => 'A', 'text' => 'Option A'],
                        ['id' => 'B', 'text' => 'Option B'],
                        ['id' => 'C', 'text' => 'Option C'],
                        ['id' => 'D', 'text' => 'Option D'],
                    ];
                    $question['correct_answer'] = 'A';
                }
                break;

            case 'true_false':
                $question['options'] = [
                    ['id' => 'true', 'text' => 'True'],
                    ['id' => 'false', 'text' => 'False'],
                ];
                $question['correct_answer'] = 'true';
                break;

            case 'essay':
                $question['options'] = [];
                $question['rubric'] = $this->generateEssayRubric($question);
                break;
        }

        return $question;
    }
}
