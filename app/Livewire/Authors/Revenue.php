<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Author;
use App\Models\BookSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class Revenue extends AppComponent
{
    public Author $author;
    public $dateRange = '30'; // days
    public $selectedPeriod = 'monthly';
    public $selectedBook = 'all';
    public $showDetails = false;

    public function mount(Author $author)
    {
        $this->author = $author;
    }

    public function render(): View
    {
        $revenueData = $this->getRevenueData();
        $chartData = $this->getChartData();
        $topBooks = $this->getTopPerformingBooks();
        $recentTransactions = $this->getRecentTransactions();
        $projections = $this->getProjections();

        return view('livewire.authors.revenue', [
            'revenueData' => $revenueData,
            'chartData' => $chartData,
            'topBooks' => $topBooks,
            'recentTransactions' => $recentTransactions,
            'projections' => $projections,
            'books' => $this->author->books()->get(),
        ]);
    }

    private function getRevenueData()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        $endDate = Carbon::now();

        // Get base query for author's book subscriptions
        $baseQuery = BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })->where('status', 'active');

        if ($this->selectedBook !== 'all') {
            $baseQuery->where('book_id', $this->selectedBook);
        }

        // Total revenue
        $totalRevenue = $baseQuery->sum('annual_fee');

        // Revenue for selected period
        $periodRevenue = $baseQuery->whereBetween('payment_completed_at', [$startDate, $endDate])
            ->sum('annual_fee');

        // Previous period for comparison
        $prevStartDate = Carbon::now()->subDays($this->dateRange * 2);
        $prevEndDate = Carbon::now()->subDays($this->dateRange);

        $previousPeriodRevenue = $baseQuery->whereBetween('payment_completed_at', [$prevStartDate, $prevEndDate])
            ->sum('annual_fee');

        // Calculate growth
        $growth = $previousPeriodRevenue > 0
            ? (($periodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : 0;

        // Monthly recurring revenue
        $monthlyRevenue = $baseQuery->whereMonth('payment_completed_at', Carbon::now()->month)
            ->whereYear('payment_completed_at', Carbon::now()->year)
            ->sum('annual_fee');

        // Active subscriptions
        $activeSubscriptions = $baseQuery->where('end_date', '>', Carbon::now())->count();

        // Average revenue per subscriber
        $averageRevenue = $activeSubscriptions > 0 ? $totalRevenue / $activeSubscriptions : 0;

        return [
            'total_revenue' => $totalRevenue,
            'period_revenue' => $periodRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'growth_percentage' => round($growth, 2),
            'active_subscriptions' => $activeSubscriptions,
            'average_revenue' => $averageRevenue,
            'conversion_rate' => $this->getConversionRate(),
        ];
    }

    private function getChartData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $dailyRevenue = BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })
        ->where('status', 'active')
        ->whereBetween('payment_completed_at', [$startDate, $endDate])
        ->select(
            DB::raw('DATE(payment_completed_at) as date'),
            DB::raw('SUM(annual_fee) as revenue'),
            DB::raw('COUNT(*) as subscriptions')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $labels = [];
        $revenues = [];
        $subscriptions = [];

        // Fill in missing dates with 0 values
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M j');

            $dayData = $dailyRevenue->firstWhere('date', $dateStr);
            $revenues[] = $dayData ? $dayData->revenue : 0;
            $subscriptions[] = $dayData ? $dayData->subscriptions : 0;

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'subscriptions' => $subscriptions,
        ];
    }

    private function getTopPerformingBooks()
    {
        return $this->author->books()
            ->withCount(['subscriptions as active_subscriptions_count' => function ($query) {
                $query->where('status', 'active')
                    ->where('end_date', '>', Carbon::now());
            }])
            ->with(['subscriptions' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->map(function ($book) {
                $totalRevenue = $book->subscriptions->sum('annual_fee');
                return [
                    'book' => $book,
                    'revenue' => $totalRevenue,
                    'subscriptions' => $book->active_subscriptions_count,
                    'average_price' => $book->subscriptions->count() > 0 ? $totalRevenue / $book->subscriptions->count() : 0,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);
    }

    private function getRecentTransactions()
    {
        return BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })
        ->where('status', 'active')
        ->whereNotNull('payment_completed_at')
        ->with(['book', 'student.user'])
        ->orderBy('payment_completed_at', 'desc')
        ->limit(10)
        ->get();
    }

    private function getProjections()
    {
        $currentMonthRevenue = BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })
        ->where('status', 'active')
        ->whereMonth('payment_completed_at', Carbon::now()->month)
        ->whereYear('payment_completed_at', Carbon::now()->year)
        ->sum('annual_fee');

        $previousMonthRevenue = BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })
        ->where('status', 'active')
        ->whereMonth('payment_completed_at', Carbon::now()->subMonth()->month)
        ->whereYear('payment_completed_at', Carbon::now()->subMonth()->year)
        ->sum('annual_fee');

        $growthRate = $previousMonthRevenue > 0
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100
            : 0;

        return [
            'next_month_projection' => $currentMonthRevenue * (1 + ($growthRate / 100)),
            'quarterly_projection' => $currentMonthRevenue * 3 * (1 + ($growthRate / 100)),
            'yearly_projection' => $currentMonthRevenue * 12 * (1 + ($growthRate / 100)),
            'growth_rate' => $growthRate,
        ];
    }

    private function getConversionRate()
    {
        // This would need to be implemented based on your visitor tracking
        // For now, returning a placeholder value
        return 15.5; // percentage
    }

    public function updateDateRange($range)
    {
        $this->dateRange = $range;
    }

    public function updatePeriod($period)
    {
        $this->selectedPeriod = $period;
    }

    public function updateBook($bookId)
    {
        $this->selectedBook = $bookId;
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }
}
