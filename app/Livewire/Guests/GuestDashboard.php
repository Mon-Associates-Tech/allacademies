<?php

namespace App\Livewire\Guests;

use App\Models\Book;
use App\Models\BookCategory as Category;
use App\Models\BookReadingProgress;
use App\Models\BookSubscription;
use App\Models\QuizSession;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GuestDashboard extends Component
{
    use WithPagination;

    public $recentBooks = [];

    public $freeBooks = [];

    public $subscribedBooks = [];

    public $recommendedBooks = [];

    public $categories = [];

    public $subscriptionStats = [];

    public $guestCapabilities = [];

    public $quickStats = [];

    public $learningStats = [];

    public $recentActivity = [];

    public $readingProgressData = [];

    public function mount(): void
    {
        $this->loadDashboardData();
        $this->loadLearningStats();
        $this->loadRecentActivity();
    }

    public function loadDashboardData(): void
    {
        $user = Auth::user();

        // Get recent free books (last 5)
        $this->recentBooks = Book::where(function ($query) {
            $query->where('is_free', true)
                ->orWhere('price', '<=', 0)
                ->orWhereNull('price')
                ->orWhere('annual_subscription_fee', '<=', 0)
                ->orWhereNull('annual_subscription_fee');
        })
            ->with(['author', 'author.user', 'bookCategory'])
            ->latest()
            ->take(6)
            ->get();

        // Get all free books count
        $this->freeBooks = Book::where(function ($query) {
            $query->where('is_free', true)
                ->orWhere('price', '<=', 0)
                ->orWhereNull('price')
                ->orWhere('annual_subscription_fee', '<=', 0)
                ->orWhereNull('annual_subscription_fee');
        })->count();

        // Get user's subscribed books

        $this->subscribedBooks = BookSubscription::where('user_id', $user->id)
            ->where('status', 'paid')
            ->with(['book', 'book.author', 'book.author.user', 'book.bookCategory'])
            ->take(3)
            ->get();

        // Get recommended books (popular paid books)
        $this->recommendedBooks = Book::where('is_free', false)
            ->with(['author', 'author.user', 'bookCategory'])
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Get categories
        $this->categories = Category::withCount('books')->take(6)->get();

        // Get subscription statistics
        $this->subscriptionStats = [
            'total_subscriptions' => BookSubscription::where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
            'free_books_available' => $this->freeBooks,
            'total_books' => Book::count(),
            'total_categories' => Category::count(),
        ];

        // Guest capabilities
        $this->guestCapabilities = [
            [
                'title' => 'Free Books Library',
                'description' => 'Access our collection of free educational books',
                'icon' => 'book',
                'route' => 'books.index',
                'color' => 'blue',
            ],
            [
                'title' => 'Premium Books',
                'description' => 'Subscribe to premium books for deeper learning',
                'icon' => 'star',
                'route' => 'books.index',
                'color' => 'yellow',
            ],
            [
                'title' => 'Self Assessments',
                'description' => 'Test your knowledge with interactive quizzes',
                'icon' => 'clipboard',
                'route' => 'learning.quiz',
                'color' => 'green',
            ],
            [
                'title' => 'AI Research Assistant',
                'description' => 'Get help with your studies using AI',
                'icon' => 'chat',
                'route' => 'academic-chat.index',
                'color' => 'purple',
            ],
            [
                'title' => 'Discussion Forums',
                'description' => 'Engage with other learners in our community',
                'icon' => 'users',
                'route' => 'guests.forums',
                'color' => 'indigo',
            ],
            [
                'title' => 'Track Progress',
                'description' => 'Monitor your learning journey and achievements',
                'icon' => 'chart',
                'route' => 'quiz.performance',
                'color' => 'red',
            ],
        ];

        // Quick stats
        $this->quickStats = [
            [
                'label' => 'Free Books',
                'value' => $this->freeBooks,
                'icon' => 'book-open',
                'color' => 'blue',
            ],
            [
                'label' => 'My Subscriptions',
                'value' => $this->subscriptionStats['total_subscriptions'],
                'icon' => 'bookmark',
                'color' => 'green',
            ],
            [
                'label' => 'Messenger Tokens',
                'value' => $user->hasActiveSubscriptionCycle() ? $user->getCurrentActiveCycle()->tokens_remaining : 0,
                'icon' => 'zap',
                'color' => 'yellow',
            ],
            [
                'label' => 'Categories',
                'value' => $this->subscriptionStats['total_categories'],
                'icon' => 'grid',
                'color' => 'purple',
            ],
        ];
    }

    public function subscribeToBook($bookId): void
    {
        $user = Auth::user();

        // Check if already subscribed
        $existingSubscription = BookSubscription::where('book_id', $bookId)
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->first();

        if ($existingSubscription) {
            $this->addError('subscription', 'You are already subscribed to this book.');

            return;
        }

        // Create subscription
        BookSubscription::create([
            'book_id' => $bookId,
            'user_id' => $user->id,
            'status' => 'paid',
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        session()->flash('success', 'Successfully subscribed to the book!');
        $this->loadDashboardData();
    }

    public function loadLearningStats(): void
    {
        $user = Auth::user();

        // Get quiz statistics
        $quizSessions = QuizSession::where('user_id', $user->id)->get();
        $completedQuizzes = $quizSessions->where('status', 'completed');

        $totalQuizAttempts = $quizSessions->count();
        $completedQuizCount = $completedQuizzes->count();
        $averageQuizScore = $completedQuizzes->count() > 0
            ? round($completedQuizzes->avg(fn ($s) => $s->results['percentage'] ?? 0), 1)
            : 0;

        // Get reading progress statistics
        $readingProgress = BookReadingProgress::where('user_id', $user->id)->get();
        $booksStarted = $readingProgress->count();
        $booksCompleted = $readingProgress->where('progress_percentage', '>=', 100)->count();
        $averageReadingProgress = $readingProgress->count() > 0
            ? round($readingProgress->avg('progress_percentage'), 1)
            : 0;

        // Calculate total reading time (in hours)
        $totalReadingMinutes = $readingProgress->sum('total_time_spent') ?? 0;
        $totalReadingHours = round($totalReadingMinutes / 60, 1);

        $this->learningStats = [
            'total_quiz_attempts' => $totalQuizAttempts,
            'completed_quizzes' => $completedQuizCount,
            'average_quiz_score' => $averageQuizScore,
            'books_started' => $booksStarted,
            'books_completed' => $booksCompleted,
            'average_reading_progress' => $averageReadingProgress,
            'total_reading_hours' => $totalReadingHours,
        ];

        // Prepare reading progress chart data
        $this->readingProgressData = $readingProgress
            ->sortByDesc('updated_at')
            ->take(5)
            ->map(function ($progress) {
                return [
                    'book_title' => $progress->book->title ?? 'Unknown Book',
                    'progress' => $progress->progress_percentage ?? 0,
                    'last_read' => $progress->updated_at?->diffForHumans() ?? 'Never',
                ];
            })
            ->values()
            ->toArray();
    }

    public function loadRecentActivity(): void
    {
        $user = Auth::user();
        $activities = collect();

        // Recent quiz completions
        $recentQuizzes = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($quiz) {
                return [
                    'type' => 'quiz',
                    'title' => 'Completed Quiz',
                    'description' => ($quiz->book?->title ?? 'Self Assessment').' - Score: '.($quiz->results['percentage'] ?? 0).'%',
                    'time' => $quiz->completed_at,
                    'icon' => 'clipboard-check',
                    'color' => 'green',
                ];
            });

        $activities = $activities->merge($recentQuizzes);

        // Recent book subscriptions
        $recentSubscriptions = BookSubscription::where('user_id', $user->id)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'subscription',
                    'title' => 'Subscribed to Book',
                    'description' => $sub->book?->title ?? 'Unknown Book',
                    'time' => $sub->created_at,
                    'icon' => 'bookmark',
                    'color' => 'blue',
                ];
            });

        $activities = $activities->merge($recentSubscriptions);

        // Recent reading activity
        $recentReading = BookReadingProgress::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($progress) {
                return [
                    'type' => 'reading',
                    'title' => 'Reading Progress',
                    'description' => ($progress->book?->title ?? 'Unknown Book').' - '.round($progress->progress_percentage ?? 0).'% complete',
                    'time' => $progress->updated_at,
                    'icon' => 'book-open',
                    'color' => 'purple',
                ];
            });

        $activities = $activities->merge($recentReading);

        // Sort by time and take top 5
        $this->recentActivity = $activities
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.guests.dashboard', [
            'recentBooks' => $this->recentBooks,
            'subscribedBooks' => $this->subscribedBooks,
            'recommendedBooks' => $this->recommendedBooks,
            'categories' => $this->categories,
            'subscriptionStats' => $this->subscriptionStats,
            'learningStats' => $this->learningStats,
            'recentActivity' => $this->recentActivity,
            'readingProgressData' => $this->readingProgressData,
        ]);
    }
}
