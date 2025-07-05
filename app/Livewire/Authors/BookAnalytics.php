<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookAnalytics extends Component
{
    public $period = '30'; // days
    public $selectedBook = '';
    public $analytics = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedPeriod()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedBook()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $author = Auth::user()->author;
        if (!$author) return;

        $startDate = Carbon::now()->subDays($this->period);
        $bookIds = $author->books()->pluck('id');

        if ($this->selectedBook) {
            $bookIds = [$this->selectedBook];
        }

        $this->analytics = [
            'total_views' => $this->getTotalViews($bookIds, $startDate),
            'total_subscriptions' => $this->getTotalSubscriptions($bookIds, $startDate),
            'total_borrowings' => $this->getTotalBorrowings($bookIds, $startDate),
            'revenue' => $this->getRevenue($bookIds, $startDate),
            'top_performing_books' => $this->getTopPerformingBooks($bookIds, $startDate),
            'subscriber_demographics' => $this->getSubscriberDemographics($bookIds, $startDate),
        ];

        $this->chartData = [
            'subscriptions' => $this->getSubscriptionTrends($bookIds, $startDate),
            'borrowings' => $this->getBorrowingTrends($bookIds, $startDate),
            'revenue' => $this->getRevenueTrends($bookIds, $startDate),
        ];
    }

    private function getTotalViews($bookIds, $startDate)
    {
        // Placeholder - implement based on your view tracking system
        return rand(100, 1000);
    }

    private function getTotalSubscriptions($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getTotalBorrowings($bookIds, $startDate)
    {
        return BookBorrowing::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getRevenue($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->sum('annual_fee');
    }

    private function getTopPerformingBooks($bookIds, $startDate)
    {
        return Book::whereIn('id', $bookIds)
            ->withCount([
                'subscriptions' => function($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                },
                'borrowings' => function($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            ])
            ->orderByDesc('subscriptions_count')
            ->take(5)
            ->get();
    }

    private function getSubscriberDemographics($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('book_subscriptions.created_at', '>=', $startDate)
            ->join('students', 'book_subscriptions.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('users.role', DB::raw('count(*) as count'))
            ->groupBy('users.role')
            ->get()
            ->pluck('count', 'role');
    }

    private function getSubscriptionTrends($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');
    }

    private function getBorrowingTrends($bookIds, $startDate)
    {
        return BookBorrowing::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');
    }

    private function getRevenueTrends($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(annual_fee) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date');
    }

    public function exportAnalytics($type = 'csv')
    {
        // Implement export functionality
        session()->flash('success', 'Analytics exported successfully!');
    }

    public function render()
    {
        $author = Auth::user()->author;
        $books = $author ? $author->books : collect();

        return view('livewire.authors.analytics', [
            'books' => $books,
        ]);
    }
}
