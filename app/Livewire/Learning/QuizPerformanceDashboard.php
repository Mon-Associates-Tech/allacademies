<?php

namespace App\Livewire\Learning;

use App\Models\Book;
use App\Models\QuizSession;
use App\Models\User;
use App\Support\GradingSystemResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class QuizPerformanceDashboard extends Component
{
    // Optional user ID for viewing other user's performance (e.g., for parents/teachers)
    public ?int $userId = null;

    // Filter properties
    public $selectedPeriod = 'all'; // all, today, week, month, quarter, year, custom

    public $selectedBookId = null;

    public $selectedDifficulty = 'all'; // all, easy, medium, hard

    public $selectedQuestionType = 'all'; // all, multiple_choice, true_false, essay, mixed

    public $minScore = null;

    public $maxScore = null;

    public $startDate = null;

    public $endDate = null;

    // UI state
    public $activeView = 'overview'; // overview, detailed, trends, comparisons

    public $selectedMetric = 'percentage'; // percentage, time, questions

    public $chartType = 'line'; // line, bar, pie

    // Available options
    public $availableBooks = [];

    public $periods = [
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'quarter' => 'This Quarter',
        'year' => 'This Year',
        'all' => 'All Time',
        'custom' => 'Custom Range',
    ];

    public $difficulties = [
        'all' => 'All Difficulties',
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
    ];

    public $questionTypes = [
        'all' => 'All Types',
        'multiple_choice' => 'Multiple Choice',
        'true_false' => 'True/False',
        'essay' => 'Essay',
        'mixed' => 'Mixed',
    ];

    // Chart data properties for Livewire chart components
    public array $bookBarLabels = [];

    public array $bookBarDatasets = [];

    public array $bookBarOptions = [];

    public array $difficultyPieLabels = [];

    public array $difficultyPieValues = [];

    public array $difficultyPieOptions = [];

    public array $typePieLabels = [];

    public array $typePieValues = [];

    public array $typePieOptions = [];

    public array $gradeBarLabels = [];

    public array $gradeBarDatasets = [];

    public array $gradeBarOptions = [];

    public float $completionGaugeValue = 0.0;

    public int $completionGaugeMin = 0;

    public int $completionGaugeMax = 100;

    public array $completionGaugeThresholds = [
        ['max' => 50, 'color' => '#ef4444', 'label' => 'Low'],
        ['max' => 80, 'color' => '#f59e0b', 'label' => 'Medium'],
        ['max' => 100, 'color' => '#10b981', 'label' => 'High'],
    ];

    public array $trendLineLabels = [];

    public array $trendLineDatasets = [];

    public array $trendLineOptions = [];

    public function mount(?int $userId = null)
    {
        $this->userId = $userId ?? Auth::id();
        $this->loadAvailableBooks();

        // Set default date range for custom
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // Initialize chart data
        $this->prepareChartData();
    }

    /**
     * Called when any filter property is updated
     */
    public function updated($property): void
    {
        // Refresh chart data when filters change
        if (in_array($property, ['selectedPeriod', 'selectedBookId', 'selectedDifficulty', 'selectedQuestionType', 'minScore', 'maxScore', 'startDate', 'endDate'])) {
            $this->prepareChartData();
        }
    }

    /**
     * Prepare all chart data for Livewire chart components
     */
    protected function prepareChartData(): void
    {
        $this->prepareBookPerformanceChart();
        $this->prepareDifficultyPieChart();
        $this->prepareQuestionTypePieChart();
        $this->prepareGradeDistributionChart();
        $this->prepareCompletionGauge();
        $this->prepareTrendLineChart();
    }

    /**
     * Prepare bar chart data for performance by book
     */
    protected function prepareBookPerformanceChart(): void
    {
        $bookData = $this->performanceByBook;

        if ($bookData->isEmpty()) {
            $this->bookBarLabels = [];
            $this->bookBarDatasets = [];

            return;
        }

        $this->bookBarLabels = $bookData->take(10)->pluck('book_title')->map(fn ($title) => \Str::limit($title, 20))->toArray();
        $barData = $bookData->take(10)->pluck('average_score')->toArray();

        $this->bookBarDatasets = [
            [
                'label' => 'Avg Score %',
                'data' => $barData,
                'backgroundColor' => '#3b82f6',
            ],
        ];
        $this->bookBarOptions = [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'max' => 100],
            ],
        ];
    }

    /**
     * Prepare pie chart data for difficulty distribution
     */
    protected function prepareDifficultyPieChart(): void
    {
        $difficultyData = $this->performanceByDifficulty;

        if ($difficultyData->isEmpty()) {
            $this->difficultyPieLabels = [];
            $this->difficultyPieValues = [];

            return;
        }

        $this->difficultyPieLabels = $difficultyData->pluck('difficulty')->toArray();
        $this->difficultyPieValues = $difficultyData->pluck('quiz_count')->toArray();
        $this->difficultyPieOptions = ['plugins' => ['legend' => ['position' => 'right']]];
    }

    /**
     * Prepare pie chart data for question type distribution
     */
    protected function prepareQuestionTypePieChart(): void
    {
        $typeData = $this->performanceByQuestionType;

        if ($typeData->isEmpty()) {
            $this->typePieLabels = [];
            $this->typePieValues = [];

            return;
        }

        $this->typePieLabels = $typeData->pluck('type')->toArray();
        $this->typePieValues = $typeData->pluck('quiz_count')->toArray();
        $this->typePieOptions = ['plugins' => ['legend' => ['position' => 'right']]];
    }

    /**
     * Prepare bar chart data for grade distribution
     */
    protected function prepareGradeDistributionChart(): void
    {
        $gradeData = $this->performanceData['grade_distribution'] ?? [];

        if (empty($gradeData)) {
            $this->gradeBarLabels = [];
            $this->gradeBarDatasets = [];

            return;
        }

        $this->gradeBarLabels = array_keys($gradeData);
        $barData = array_values($gradeData);

        $this->gradeBarDatasets = [
            [
                'label' => 'Quiz Count',
                'data' => $barData,
                'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#f97316', '#ef4444'],
            ],
        ];
        $this->gradeBarOptions = [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare gauge chart data for completion rate
     */
    protected function prepareCompletionGauge(): void
    {
        $this->completionGaugeValue = (float) ($this->performanceData['completion_rate'] ?? 0);
        $this->completionGaugeMin = 0;
        $this->completionGaugeMax = 100;
        $this->completionGaugeThresholds = [
            ['max' => 50, 'color' => '#ef4444', 'label' => 'Low'],
            ['max' => 80, 'color' => '#f59e0b', 'label' => 'Medium'],
            ['max' => 100, 'color' => '#10b981', 'label' => 'High'],
        ];
    }

    /**
     * Prepare line chart data for performance trends
     */
    protected function prepareTrendLineChart(): void
    {
        $trendData = $this->timeSeriesData;

        if (empty($trendData)) {
            $this->trendLineLabels = [];
            $this->trendLineDatasets = [];

            return;
        }

        $this->trendLineLabels = array_column($trendData, 'period');
        $scoreData = array_column($trendData, 'average_score');

        $this->trendLineDatasets = [
            [
                'label' => 'Avg Score %',
                'data' => $scoreData,
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.3,
                'fill' => true,
            ],
        ];
        $this->trendLineOptions = [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales' => [
                'y' => ['beginAtZero' => true, 'max' => 100],
            ],
        ];
    }

    #[Computed]
    public function performanceData()
    {
        $cacheKey = $this->getCacheKey('performance_data');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            $sessions = $query->get();

            Log::info('Performance Data Query', [
                'user_id' => $this->userId,
                'sessions_count' => $sessions->count(),
                'filters' => [
                    'period' => $this->selectedPeriod,
                    'book_id' => $this->selectedBookId,
                    'difficulty' => $this->selectedDifficulty,
                ],
            ]);

            if ($sessions->isEmpty()) {
                return $this->getEmptyPerformanceData();
            }

            return [
                'total_quizzes' => $sessions->count(),
                'average_score' => round($sessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                'highest_score' => $sessions->max(fn ($s) => $s->results['percentage'] ?? 0),
                'lowest_score' => $sessions->min(fn ($s) => $s->results['percentage'] ?? 0),
                'total_questions_answered' => $sessions->sum(fn ($s) => $s->results['total_questions'] ?? 0),
                'total_correct_answers' => $sessions->sum(fn ($s) => $s->results['correct_answers'] ?? 0),
                'average_time_taken' => round($sessions->whereNotNull('time_taken')->avg('time_taken'), 2),
                'total_time_spent' => $sessions->whereNotNull('time_taken')->sum('time_taken'),
                'completion_rate' => $this->calculateCompletionRate(),
                'improvement_trend' => $this->calculateImprovementTrend($sessions),
                'grade_distribution' => $this->calculateGradeDistribution($sessions),
            ];
        });
    }

    #[Computed]
    public function performanceByBook()
    {
        $cacheKey = $this->getCacheKey('performance_by_book');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            $sessions = $query->get();

            // Separate book-based and file-based quizzes
            $bookBased = $sessions->filter(fn ($session) => $session->book_id !== null)->load('book');
            $fileBased = $sessions->filter(fn ($session) => $session->book_id === null);

            $results = collect();

            // Process book-based quizzes
            if ($bookBased->isNotEmpty()) {
                $bookResults = $bookBased->groupBy('book_id')
                    ->map(function ($bookSessions) {
                        $book = $bookSessions->first()->book;

                        if (! $book) {
                            return null;
                        }

                        return [
                            'book_id' => $book->id,
                            'book_title' => $book->title,
                            'author' => $book->author->name ?? 'Unknown',
                            'quiz_count' => $bookSessions->count(),
                            'average_score' => round($bookSessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                            'best_score' => $bookSessions->max(fn ($s) => $s->results['percentage'] ?? 0),
                            'last_attempt' => $bookSessions->max('completed_at'),
                            'improvement' => $this->calculateBookImprovement($bookSessions),
                            'type' => 'book',
                        ];
                    })
                    ->filter() // Remove nulls
                    ->values();

                $results = $results->merge($bookResults);
            }

            // Add file-based quizzes as a single group if any exist
            if ($fileBased->isNotEmpty()) {
                $results->push([
                    'book_id' => null,
                    'book_title' => 'Uploaded Content',
                    'author' => 'Various',
                    'quiz_count' => $fileBased->count(),
                    'average_score' => round($fileBased->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                    'best_score' => $fileBased->max(fn ($s) => $s->results['percentage'] ?? 0),
                    'last_attempt' => $fileBased->max('completed_at'),
                    'improvement' => $this->calculateBookImprovement($fileBased),
                    'type' => 'file',
                ]);
            }

            return $results->sortByDesc('average_score')->values();
        });
    }

    #[Computed]
    public function recentQuizzes()
    {
        return $this->getBaseQuery()
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(function ($session) {
                // Load book relationship if it exists
                if ($session->book_id && ! $session->relationLoaded('book')) {
                    $session->load('book.author');
                }

                return [
                    'id' => $session->id,
                    'book_title' => $session->book ? $session->book->title : ($session->context['book_title'] ?? 'Uploaded Content'),
                    'author' => $session->book && $session->book->author ? $session->book->author->name : ($session->context['author'] ?? 'N/A'),
                    'difficulty' => ucfirst($session->difficulty),
                    'question_type' => $this->questionTypes[$session->question_type] ?? ucfirst($session->question_type),
                    'score' => $session->results['percentage'] ?? 0,
                    'grade' => $this->calculateLetterGrade($session->results['percentage'] ?? 0),
                    'questions' => $session->results['total_questions'] ?? 0,
                    'correct' => $session->results['correct_answers'] ?? 0,
                    'time_taken' => $session->time_taken,
                    'completed_at' => $session->completed_at,
                ];
            });
    }

    #[Computed]
    public function strengthsAndWeaknesses()
    {
        $cacheKey = $this->getCacheKey('strengths_weaknesses');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();
            $sessions = $query->get();

            if ($sessions->isEmpty()) {
                return ['strengths' => [], 'weaknesses' => []];
            }

            // Analyze question details across all sessions
            $questionTypePerformance = [];

            foreach ($sessions as $session) {
                $questionDetails = $session->results['question_details'] ?? [];

                foreach ($questionDetails as $detail) {
                    $type = $detail['question_type'] ?? 'unknown';

                    if (! isset($questionTypePerformance[$type])) {
                        $questionTypePerformance[$type] = [
                            'total' => 0,
                            'correct' => 0,
                            'type_name' => $this->questionTypes[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                        ];
                    }

                    $questionTypePerformance[$type]['total']++;
                    if ($detail['is_correct'] ?? false) {
                        $questionTypePerformance[$type]['correct']++;
                    }
                }
            }

            // Calculate percentages and categorize
            $strengths = [];
            $weaknesses = [];

            foreach ($questionTypePerformance as $type => $data) {
                $percentage = $data['total'] > 0 ? ($data['correct'] / $data['total']) * 100 : 0;

                $item = [
                    'type' => $data['type_name'],
                    'accuracy' => round($percentage, 2),
                    'total_questions' => $data['total'],
                    'correct_answers' => $data['correct'],
                ];

                if ($percentage >= 75) {
                    $strengths[] = $item;
                } elseif ($percentage < 60) {
                    $weaknesses[] = $item;
                }
            }

            // Sort by accuracy
            usort($strengths, fn ($a, $b) => $b['accuracy'] <=> $a['accuracy']);
            usort($weaknesses, fn ($a, $b) => $a['accuracy'] <=> $b['accuracy']);

            return [
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
            ];
        });
    }

    protected function getBaseQuery()
    {
        $query = QuizSession::where('user_id', $this->userId)
            ->where('status', 'completed');

        // Apply filters
        if ($this->selectedBookId) {
            $query->where('book_id', $this->selectedBookId);
        }

        if ($this->selectedDifficulty !== 'all') {
            $query->where('difficulty', $this->selectedDifficulty);
        }

        if ($this->selectedQuestionType !== 'all') {
            $query->where('question_type', $this->selectedQuestionType);
        }

        // Apply date range
        $dateRange = $this->getDateRange();
        if ($dateRange) {
            $query->whereBetween('completed_at', $dateRange);
        }

        // Apply score range
        if ($this->minScore !== null || $this->maxScore !== null) {
            $minScore = $this->minScore ?? 0;
            $maxScore = $this->maxScore ?? 100;

            // Use whereRaw for JSON extraction if using MySQL
            if (config('database.default') === 'mysql') {
                $query->whereRaw('JSON_EXTRACT(results, "$.percentage") >= ?', [$minScore])
                    ->whereRaw('JSON_EXTRACT(results, "$.percentage") <= ?', [$maxScore]);
            } else {
                // Fallback: load all and filter in PHP (less efficient but works)
                $query->get()->filter(function ($session) use ($minScore, $maxScore) {
                    $percentage = $session->results['percentage'] ?? 0;

                    return $percentage >= $minScore && $percentage <= $maxScore;
                });
            }
        }

        return $query;
    }

    protected function loadAvailableBooks()
    {
        // Get books that have completed quiz sessions for this user
        $bookIds = QuizSession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereNotNull('book_id')
            ->distinct()
            ->pluck('book_id');

        if ($bookIds->isEmpty()) {
            $this->availableBooks = collect();

            return;
        }

        $this->availableBooks = Book::whereIn('id', $bookIds)
            ->with('author')
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function targetUser()
    {
        return Cache::remember("user_{$this->userId}", 300, function () {
            return User::find($this->userId);
        });
    }

    #[Computed]
    public function performanceData1()
    {
        $cacheKey = $this->getCacheKey('performance_data');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            $sessions = $query->get();

            if ($sessions->isEmpty()) {
                return $this->getEmptyPerformanceData();
            }

            return [
                'total_quizzes' => $sessions->count(),
                'average_score' => round($sessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                'highest_score' => $sessions->max(fn ($s) => $s->results['percentage'] ?? 0),
                'lowest_score' => $sessions->min(fn ($s) => $s->results['percentage'] ?? 0),
                'total_questions_answered' => $sessions->sum(fn ($s) => $s->results['total_questions'] ?? 0),
                'total_correct_answers' => $sessions->sum(fn ($s) => $s->results['correct_answers'] ?? 0),
                'average_time_taken' => round($sessions->whereNotNull('time_taken')->avg('time_taken'), 2),
                'total_time_spent' => $sessions->whereNotNull('time_taken')->sum('time_taken'),
                'completion_rate' => $this->calculateCompletionRate(),
                'improvement_trend' => $this->calculateImprovementTrend($sessions),
                'grade_distribution' => $this->calculateGradeDistribution($sessions),
            ];
        });
    }

    #[Computed]
    public function performanceByBook1()
    {
        $cacheKey = $this->getCacheKey('performance_by_book');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            return $query->with('book')
                ->get()
                ->filter(fn ($session) => $session->book !== null)
                ->groupBy('book_id')
                ->map(function ($bookSessions) {
                    $book = $bookSessions->first()->book;

                    return [
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'author' => $book->author->name ?? 'Unknown',
                        'quiz_count' => $bookSessions->count(),
                        'average_score' => round($bookSessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                        'best_score' => $bookSessions->max(fn ($s) => $s->results['percentage'] ?? 0),
                        'last_attempt' => $bookSessions->max('completed_at'),
                        'improvement' => $this->calculateBookImprovement($bookSessions),
                    ];
                })
                ->sortByDesc('average_score')
                ->values();
        });
    }

    #[Computed]
    public function performanceByDifficulty()
    {
        $cacheKey = $this->getCacheKey('performance_by_difficulty');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            return $query->get()
                ->groupBy('difficulty')
                ->map(function ($difficultySessions, $difficulty) {
                    return [
                        'difficulty' => ucfirst($difficulty),
                        'quiz_count' => $difficultySessions->count(),
                        'average_score' => round($difficultySessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                        'pass_rate' => round(
                            $difficultySessions->filter(fn ($s) => ($s->results['percentage'] ?? 0) >= 70)->count() /
                            max($difficultySessions->count(), 1) * 100,
                            2
                        ),
                    ];
                })
                ->values();
        });
    }

    #[Computed]
    public function performanceByQuestionType()
    {
        $cacheKey = $this->getCacheKey('performance_by_type');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            return $query->get()
                ->groupBy('question_type')
                ->map(function ($typeSessions, $type) {
                    return [
                        'type' => $this->questionTypes[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                        'quiz_count' => $typeSessions->count(),
                        'average_score' => round($typeSessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                        'average_time' => round($typeSessions->whereNotNull('time_taken')->avg('time_taken'), 2),
                    ];
                })
                ->values();
        });
    }

    #[Computed]
    public function timeSeriesData()
    {
        $cacheKey = $this->getCacheKey('time_series');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();

            $sessions = $query->orderBy('completed_at')->get();

            Log::info('Time Series Data Query', [
                'user_id' => $this->userId,
                'sessions_count' => $sessions->count(),
                'period' => $this->selectedPeriod,
                'first_session' => $sessions->first()?->completed_at,
                'last_session' => $sessions->last()?->completed_at,
            ]);

            if ($sessions->isEmpty()) {
                Log::warning('No sessions found for time series', [
                    'user_id' => $this->userId,
                    'filters' => [
                        'period' => $this->selectedPeriod,
                        'book_id' => $this->selectedBookId,
                        'difficulty' => $this->selectedDifficulty,
                    ],
                ]);

                return [];
            }

            // Group by appropriate time interval based on period
            $interval = $this->getTimeInterval();

            Log::info('Time interval format', ['interval' => $interval]);

            $grouped = $sessions->groupBy(function ($session) use ($interval) {
                $formatted = $session->completed_at->format($interval);
                Log::info('Formatting session date', [
                    'completed_at' => $session->completed_at,
                    'formatted' => $formatted,
                    'interval' => $interval,
                ]);

                return $formatted;
            });

            $result = $grouped->map(function ($periodSessions, $period) {
                return [
                    'period' => $period,
                    'quiz_count' => $periodSessions->count(),
                    'average_score' => round($periodSessions->avg(fn ($s) => $s->results['percentage'] ?? 0), 2),
                    'total_questions' => $periodSessions->sum(fn ($s) => $s->results['total_questions'] ?? 0),
                ];
            })->values();

            Log::info('Time series result', [
                'count' => $result->count(),
                'data' => $result->toArray(),
            ]);

            return $result->toArray();
        });
    }

    protected function getTimeInterval(): string
    {
        $interval = match ($this->selectedPeriod) {
            'today' => 'H:00', // Hour
            'week' => 'Y-m-d', // Day
            'month' => 'Y-m-d', // Day
            'quarter', 'year' => 'Y-m', // Month
            'custom' => 'Y-m-d', // Day for custom range
            default => 'Y-m-d', // Day for 'all'
        };

        Log::info('Time interval determined', [
            'selected_period' => $this->selectedPeriod,
            'interval' => $interval,
        ]);

        return $interval;
    }

    #[Computed]
    public function recentQuizzes1()
    {
        return $this->getBaseQuery()
            ->with('book')
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'book_title' => $session->book ? $session->book->title : 'Uploaded Content',
                    'author' => $session->book && $session->book->author ? $session->book->author->name : 'N/A',
                    'difficulty' => ucfirst($session->difficulty),
                    'question_type' => $this->questionTypes[$session->question_type] ?? ucfirst($session->question_type),
                    'score' => $session->results['percentage'] ?? 0,
                    'grade' => $this->calculateLetterGrade($session->results['percentage'] ?? 0),
                    'questions' => $session->results['total_questions'] ?? 0,
                    'correct' => $session->results['correct_answers'] ?? 0,
                    'time_taken' => $session->time_taken,
                    'completed_at' => $session->completed_at,
                ];
            });
    }

    #[Computed]
    public function strengthsAndWeaknesses1()
    {
        $cacheKey = $this->getCacheKey('strengths_weaknesses');

        return Cache::remember($cacheKey, 300, function () {
            $query = $this->getBaseQuery();
            $sessions = $query->get();

            if ($sessions->isEmpty()) {
                return ['strengths' => [], 'weaknesses' => []];
            }

            // Analyze question details across all sessions
            $questionTypePerformance = [];

            foreach ($sessions as $session) {
                $questionDetails = $session->results['question_details'] ?? [];

                foreach ($questionDetails as $detail) {
                    $type = $detail['question_type'] ?? 'unknown';

                    if (! isset($questionTypePerformance[$type])) {
                        $questionTypePerformance[$type] = [
                            'total' => 0,
                            'correct' => 0,
                            'type_name' => $this->questionTypes[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                        ];
                    }

                    $questionTypePerformance[$type]['total']++;
                    if ($detail['is_correct'] ?? false) {
                        $questionTypePerformance[$type]['correct']++;
                    }
                }
            }

            // Calculate percentages and categorize
            $strengths = [];
            $weaknesses = [];

            foreach ($questionTypePerformance as $type => $data) {
                $percentage = $data['total'] > 0 ? ($data['correct'] / $data['total']) * 100 : 0;

                $item = [
                    'type' => $data['type_name'],
                    'accuracy' => round($percentage, 2),
                    'total_questions' => $data['total'],
                    'correct_answers' => $data['correct'],
                ];

                if ($percentage >= 75) {
                    $strengths[] = $item;
                } elseif ($percentage < 60) {
                    $weaknesses[] = $item;
                }
            }

            // Sort by accuracy
            usort($strengths, fn ($a, $b) => $b['accuracy'] <=> $a['accuracy']);
            usort($weaknesses, fn ($a, $b) => $a['accuracy'] <=> $b['accuracy']);

            return [
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
            ];
        });
    }

    protected function getBaseQuery1()
    {
        $query = QuizSession::where('user_id', $this->userId)
            ->where('status', 'completed');

        // Apply filters
        if ($this->selectedBookId) {
            $query->where('book_id', $this->selectedBookId);
        }

        if ($this->selectedDifficulty !== 'all') {
            $query->where('difficulty', $this->selectedDifficulty);
        }

        if ($this->selectedQuestionType !== 'all') {
            $query->where('question_type', $this->selectedQuestionType);
        }

        // Apply date range
        $dateRange = $this->getDateRange();
        if ($dateRange) {
            $query->whereBetween('completed_at', $dateRange);
        }

        // Apply score range
        if ($this->minScore !== null || $this->maxScore !== null) {
            $query->whereRaw('JSON_EXTRACT(results, "$.percentage") >= ?', [$this->minScore ?? 0])
                ->whereRaw('JSON_EXTRACT(results, "$.percentage") <= ?', [$this->maxScore ?? 100]);
        }

        return $query;
    }

    protected function getDateRange(): ?array
    {
        switch ($this->selectedPeriod) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'quarter':
                return [now()->startOfQuarter(), now()->endOfQuarter()];
            case 'year':
                return [now()->startOfYear(), now()->endOfYear()];
            case 'custom':
                if ($this->startDate && $this->endDate) {
                    return [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay(),
                    ];
                }

                return null;
            case 'all':
            default:
                return null;
        }
    }

    protected function getTimeInterval1(): string
    {
        switch ($this->selectedPeriod) {
            case 'today':
                return 'H:00'; // Hour
            case 'week':
                return 'Y-m-d'; // Day
            case 'month':
                return 'Y-m-d'; // Day
            case 'quarter':
            case 'year':
                return 'Y-m'; // Month
            default:
                return 'Y-m-d'; // Day
        }
    }

    protected function calculateCompletionRate(): float
    {
        $total = QuizSession::where('user_id', $this->userId)->count();
        $completed = QuizSession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    protected function calculateImprovementTrend($sessions): array
    {
        if ($sessions->count() < 2) {
            return ['trend' => 'neutral', 'change' => 0];
        }

        $ordered = $sessions->sortBy('completed_at');
        $firstHalf = $ordered->take((int) ceil($ordered->count() / 2));
        $secondHalf = $ordered->slice((int) ceil($ordered->count() / 2));

        $firstAvg = $firstHalf->avg(fn ($s) => $s->results['percentage'] ?? 0);
        $secondAvg = $secondHalf->avg(fn ($s) => $s->results['percentage'] ?? 0);

        $change = round($secondAvg - $firstAvg, 2);

        return [
            'trend' => $change > 5 ? 'improving' : ($change < -5 ? 'declining' : 'stable'),
            'change' => $change,
        ];
    }

    protected function calculateGradeDistribution($sessions): array
    {
        // Get all available grades for the user's grading system
        $allGrades = GradingSystemResolver::getAllGrades($this->targetUser);

        // Initialize distribution with all grades set to 0
        $distribution = [];
        foreach ($allGrades as $gradeInfo) {
            $gradeKey = (string) $gradeInfo['grade'];
            $distribution[$gradeKey] = 0;
        }

        // Count sessions per grade
        foreach ($sessions as $session) {
            $percentage = $session->results['percentage'] ?? 0;
            $grade = (string) $this->calculateLetterGrade($percentage);
            if (isset($distribution[$grade])) {
                $distribution[$grade]++;
            }
        }

        return $distribution;
    }

    protected function calculateBookImprovement($sessions): float
    {
        if ($sessions->count() < 2) {
            return 0;
        }

        $ordered = $sessions->sortBy('completed_at');
        $first = $ordered->first()->results['percentage'] ?? 0;
        $last = $ordered->last()->results['percentage'] ?? 0;

        return round($last - $first, 2);
    }

    /**
     * Calculate the grade for a given percentage using the new grading system.
     *
     * @return string|int The grade value
     */
    protected function calculateLetterGrade(float $percentage): string|int
    {
        $user = $this->targetUser;
        $gradeInfo = GradingSystemResolver::getGrade($user, $percentage);

        return $gradeInfo['grade'];
    }

    /**
     * Get the full grade information for a given percentage.
     *
     * @return array{grade: string|int, interpretation: string, is_passing: bool, system: string}
     */
    public function getGrade(float $percentage): array
    {
        return GradingSystemResolver::getGrade($this->targetUser, $percentage);
    }

    /**
     * Get the grading system name for the current user.
     */
    public function getGradingSystemName(): string
    {
        return GradingSystemResolver::getSystemName($this->targetUser);
    }

    protected function getEmptyPerformanceData(): array
    {
        // Build empty grade distribution based on user's grading system
        $allGrades = GradingSystemResolver::getAllGrades($this->targetUser);
        $emptyDistribution = [];
        foreach ($allGrades as $gradeInfo) {
            $gradeKey = (string) $gradeInfo['grade'];
            $emptyDistribution[$gradeKey] = 0;
        }

        return [
            'total_quizzes' => 0,
            'average_score' => 0,
            'highest_score' => 0,
            'lowest_score' => 0,
            'total_questions_answered' => 0,
            'total_correct_answers' => 0,
            'average_time_taken' => 0,
            'total_time_spent' => 0,
            'completion_rate' => 0,
            'improvement_trend' => ['trend' => 'neutral', 'change' => 0],
            'grade_distribution' => $emptyDistribution,
        ];
    }

    protected function getCacheKey(string $suffix): string
    {
        return sprintf(
            'quiz_performance_%s_%s_%s_%s_%s_%s_%s_%s',
            $this->userId,
            $this->selectedPeriod,
            $this->selectedBookId ?? 'all',
            $this->selectedDifficulty,
            $this->selectedQuestionType,
            $this->minScore ?? 'min',
            $this->maxScore ?? 'max',
            $suffix
        );
    }

    public function resetFilters()
    {
        $this->selectedPeriod = 'all';
        $this->selectedBookId = null;
        $this->selectedDifficulty = 'all';
        $this->selectedQuestionType = 'all';
        $this->minScore = null;
        $this->maxScore = null;
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function exportPerformanceData()
    {
        $data = [
            'user' => $this->targetUser->name,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'filters' => [
                'period' => $this->selectedPeriod,
                'book' => $this->selectedBookId ? Book::find($this->selectedBookId)->title : 'All Books',
                'difficulty' => $this->selectedDifficulty,
                'question_type' => $this->selectedQuestionType,
            ],
            'performance' => $this->performanceData,
            'by_book' => $this->performanceByBook,
            'by_difficulty' => $this->performanceByDifficulty,
            'by_type' => $this->performanceByQuestionType,
            'recent_quizzes' => $this->recentQuizzes,
        ];

        $this->dispatch('download-performance-report', $data);
    }

    public function render()
    {
        return view('livewire.learning.quiz-performance-dashboard');
    }
}
