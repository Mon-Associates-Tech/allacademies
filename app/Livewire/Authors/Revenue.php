<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Author;
use App\Models\BookSubscription;
use App\Models\Payment;
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

    public function mount(?Author $author)
    {
        $this->author = auth()->user()->author;

        if (!$this->author) {
            session()->flash('error', 'Author profile not found.');
            return redirect()->route('dashboard');
        }
    }

    public function render(): View
    {
        $revenueData = $this->getRevenueData();
        $chartData = $this->getChartData();
        $topBooks = $this->getTopPerformingBooks();
        $recentTransactions = $this->getRecentTransactions();
        $projections = $this->getProjections();
        $hasSubaccount = $this->author->subaccount !== null;

        return view('livewire.authors.revenue', [
            'revenueData' => $revenueData,
            'chartData' => $chartData,
            'topBooks' => $topBooks,
            'recentTransactions' => $recentTransactions,
            'projections' => $projections,
            'books' => $this->author->books()->get(),
            'hasSubaccount' => $hasSubaccount,
        ]);
    }

    private function getRevenueData()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        $endDate = Carbon::now();

        // Get base query for author's book payments
        $baseQuery = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) {
                $query->where('author_id', $this->author->id);
            });

        if ($this->selectedBook !== 'all') {
            $baseQuery->whereHas('bookSubscription', function($query) {
                $query->where('book_id', $this->selectedBook);
            });
        }

        // Get all payments
        $allPayments = $baseQuery->with('bookSubscription')->get();

        // Calculate revenue with 98% split
        $totalGrossRevenue = $allPayments->sum('amount');
        $totalNetRevenue = $allPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });
        $totalPlatformFees = $totalGrossRevenue - $totalNetRevenue;

        // Active subscriptions
        $activeSubscriptions = BookSubscription::whereHas('book', function ($query) {
            $query->where('author_id', $this->author->id);
        })
            ->where('status', 'paid')
            ->where('end_date', '>', Carbon::now())
            ->count();

        // Period revenue
        $periodPayments = $baseQuery
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $periodGrossRevenue = $periodPayments->sum('amount');
        $periodNetRevenue = $periodPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // Previous period for comparison
        $prevStartDate = Carbon::now()->subDays($this->dateRange * 2);
        $prevEndDate = Carbon::now()->subDays($this->dateRange);

        $previousPeriodPayments = $baseQuery
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->get();

        $previousPeriodRevenue = $previousPeriodPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // Calculate growth
        $growth = $previousPeriodRevenue > 0
            ? (($periodNetRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : 0;

        // Monthly revenue
        $monthlyPayments = $baseQuery
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        $monthlyNetRevenue = $monthlyPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // Average revenue per subscriber
        $averageRevenue = $activeSubscriptions > 0 ? $totalNetRevenue / $activeSubscriptions : 0;

        return [
            'total_gross_revenue' => $totalGrossRevenue,
            'total_net_revenue' => $totalNetRevenue,
            'total_platform_fees' => $totalPlatformFees,
            'period_gross_revenue' => $periodGrossRevenue,
            'period_net_revenue' => $periodNetRevenue,
            'monthly_revenue' => $monthlyNetRevenue,
            'growth_percentage' => round($growth, 2),
            'active_subscriptions' => $activeSubscriptions,
            'average_revenue' => $averageRevenue,
            'conversion_rate' => $this->getConversionRate(),
            'total_payments' => $allPayments->count(),
        ];
    }

    private function getChartData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $dailyRevenue = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) {
                $query->where('author_id', $this->author->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $grossRevenues = [];
        $netRevenues = [];
        $payments = [];

        // Fill in missing dates with 0 values
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M j');

            // Get payments for this date
            $dayPayments = Payment::query()
                ->where('status', 'succeeded')
                ->whereHas('bookSubscription.book', function ($query) {
                    $query->where('author_id', $this->author->id);
                })
                ->whereDate('created_at', $dateStr)
                ->get();

            $dayGross = $dayPayments->sum('amount');
            $dayNet = $dayPayments->sum(function($payment) {
                return $payment->author_amount ?: ($payment->amount * 0.98);
            });

            $grossRevenues[] = $dayGross;
            $netRevenues[] = $dayNet;
            $payments[] = $dayPayments->count();

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'gross_revenues' => $grossRevenues,
            'net_revenues' => $netRevenues,
            'payments' => $payments,
        ];
    }

    private function getTopPerformingBooks()
    {
        return $this->author->books()
            ->withCount(['subscriptions as active_subscriptions_count' => function ($query) {
                $query->where('status', 'paid')
                    ->where('end_date', '>', Carbon::now());
            }])
            ->get()
            ->map(function ($book) {
                // Get all payments for this book
                $payments = Payment::query()
                    ->where('status', 'succeeded')
                    ->whereHas('bookSubscription', function($query) use ($book) {
                        $query->where('book_id', $book->id);
                    })
                    ->get();

                $grossRevenue = $payments->sum('amount');
                $netRevenue = $payments->sum(function($payment) {
                    return $payment->author_amount ?: ($payment->amount * 0.98);
                });

                return [
                    'book' => $book,
                    'gross_revenue' => $grossRevenue,
                    'net_revenue' => $netRevenue,
                    'subscriptions' => $book->active_subscriptions_count,
                    'average_price' => $payments->count() > 0 ? $netRevenue / $payments->count() : 0,
                ];
            })
            ->sortByDesc('net_revenue')
            ->take(5);
    }

    private function getRecentTransactions()
    {
        return Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) {
                $query->where('author_id', $this->author->id);
            })
            ->with(['bookSubscription.book', 'bookSubscription.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($payment) {
                return [
                    'payment' => $payment,
                    'gross_amount' => $payment->amount,
                    'net_amount' => $payment->author_amount ?: ($payment->amount * 0.98),
                    'platform_fee' => $payment->platform_amount ?: ($payment->amount * 0.02),
                ];
            });
    }

    private function getProjections()
    {
        // Get payments for current month
        $currentMonthPayments = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) {
                $query->where('author_id', $this->author->id);
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        $currentMonthRevenue = $currentMonthPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

        // Get payments for previous month
        $previousMonthPayments = Payment::query()
            ->where('status', 'succeeded')
            ->whereHas('bookSubscription.book', function ($query) {
                $query->where('author_id', $this->author->id);
            })
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->get();

        $previousMonthRevenue = $previousMonthPayments->sum(function($payment) {
            return $payment->author_amount ?: ($payment->amount * 0.98);
        });

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
        // Placeholder for visitor tracking
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
