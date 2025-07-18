<?php

namespace App\Livewire\Subscribers;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookCategory as Category;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SubscriberDashboard extends Component
{
    use WithPagination;

    public $recentBooks = [];
    public $freeBooks = [];
    public $subscribedBooks = [];
    public $recommendedBooks = [];
    public $categories = [];
    public $subscriptionStats = [];

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
            ->take(3)
            ->get();

        // Get all free books count
        $this->freeBooks = Book::where('is_free', true)->count();

        // Get user's subscribed books if they have a student profile
        if ($user->student) {
            $this->subscribedBooks = BookSubscription::where('student_id', $user->student->id)
                ->where('status', 'active')
                ->with(['book', 'book.author', 'book.author.user', 'book.category'])
                ->take(3)
                ->get();
        }

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
            'total_subscriptions' => $user->student ? BookSubscription::where('student_id', $user->student->id)
                ->where('status', 'active')
                ->count() : 0,
            'free_books_available' => $this->freeBooks,
            'total_books' => Book::count(),
            'total_categories' => Category::count()
        ];
    }

    public function subscribeToBook($bookId)
    {
        $user = Auth::user();

        if (!$user->student) {
            $this->addError('subscription', 'Please complete your student profile to subscribe to books.');
            return;
        }

        // Check if already subscribed
        $existingSubscription = BookSubscription::where('book_id', $bookId)
            ->where('student_id', $user->student->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            $this->addError('subscription', 'You are already subscribed to this book.');
            return;
        }

        // Create subscription
        BookSubscription::create([
            'book_id' => $bookId,
            'student_id' => $user->student->id,
            'status' => 'active',
            'subscription_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);

        session()->flash('success', 'Successfully subscribed to the book!');
        $this->loadDashboardData();
    }

    public function render(): View
    {
        return view('livewire.subscribers.dashboard', [
            'recentBooks' => $this->recentBooks,
            'subscribedBooks' => $this->subscribedBooks,
            'recommendedBooks' => $this->recommendedBooks,
            'categories' => $this->categories,
            'subscriptionStats' => $this->subscriptionStats
        ]);
    }
}
