<?php

namespace App\Livewire\Guests;

use App\Models\Book;
use App\Models\BookCategory as Category;
use App\Models\BookSubscription;
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

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();

        // Get recent free books (last 5)
        $this->recentBooks = Book::where('is_free', true)
            ->with(['author', 'author.user', 'bookCategory'])
            ->latest()
            ->take(4)
            ->get();

        // Get all free books count
        $this->freeBooks = Book::where('is_free', true)->count();

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
            'total_categories' => Category::count()
        ];

        // Guest capabilities
        $this->guestCapabilities = [
            [
                'title' => 'Free Books Library',
                'description' => 'Access our collection of free educational books',
                'icon' => 'book',
                'route' => 'books.index',
                'color' => 'blue'
            ],
            [
                'title' => 'Premium Books',
                'description' => 'Subscribe to premium books for deeper learning',
                'icon' => 'star',
                'route' => 'books.index',
                'color' => 'yellow'
            ],
            [
                'title' => 'Self Assessments',
                'description' => 'Test your knowledge with interactive quizzes',
                'icon' => 'clipboard',
                'route' => 'learning.quiz',
                'color' => 'green'
            ],
            [
                'title' => 'AI Research Assistant',
                'description' => 'Get help with your studies using AI',
                'icon' => 'chat',
                'route' => 'academic-chat.index',
                'color' => 'purple'
            ],
            [
                'title' => 'Discussion Forums',
                'description' => 'Engage with other learners in our community',
                'icon' => 'users',
                'route' => 'guests.forums',
                'color' => 'indigo'
            ],
            [
                'title' => 'Track Progress',
                'description' => 'Monitor your learning journey and achievements',
                'icon' => 'chart',
                'route' => 'quiz.performance',
                'color' => 'red'
            ]
        ];

        // Quick stats
        $this->quickStats = [
            [
                'label' => 'Free Books',
                'value' => $this->freeBooks,
                'icon' => 'book-open',
                'color' => 'blue'
            ],
            [
                'label' => 'My Subscriptions',
                'value' => $this->subscriptionStats['total_subscriptions'],
                'icon' => 'bookmark',
                'color' => 'green'
            ],
            [
                'label' => 'Messenger Tokens',
                'value' => $user->hasActiveSubscriptionCycle() ? $user->getCurrentActiveCycle()->tokens_remaining : 0,
                'icon' => 'zap',
                'color' => 'yellow'
            ],
            [
                'label' => 'Categories',
                'value' => $this->subscriptionStats['total_categories'],
                'icon' => 'grid',
                'color' => 'purple'
            ]
        ];
    }

    public function subscribeToBook($bookId)
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
            'subscription_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);

        session()->flash('success', 'Successfully subscribed to the book!');
        $this->loadDashboardData();
    }

    public function render(): View
    {
        return view('livewire.guests.dashboard', [
            'recentBooks' => $this->recentBooks,
            'subscribedBooks' => $this->subscribedBooks,
            'recommendedBooks' => $this->recommendedBooks,
            'categories' => $this->categories,
            'subscriptionStats' => $this->subscriptionStats
        ]);
    }
}
