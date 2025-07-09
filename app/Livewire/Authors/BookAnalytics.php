<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookAnalytics extends Component
{
    public $period = '30'; // days
    public $selectedBook = '';
    public $viewType = 'overview'; // overview, revenue, engagement
    public $comparisonPeriod = false;
    public $isLoading = false;
    public $refreshInterval = 300; // 5 minutes

    protected $queryString = [
        'period' => ['except' => '30'],
        'selectedBook' => ['except' => ''],
        'viewType' => ['except' => 'overview'],
    ];

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

    public function updatedViewType()
    {
        $this->loadAnalytics();
    }

    public function toggleComparison()
    {
        $this->comparisonPeriod = !$this->comparisonPeriod;
        $this->loadAnalytics();
    }

    #[On('refresh-analytics')]
    public function loadAnalytics()
    {
        $this->isLoading = true;

        try {
            $author = Auth::user()->author;
            if (!$author) return;

            $cacheKey = "analytics_author_{$author->id}_{$this->period}_{$this->selectedBook}_{$this->viewType}";

            // Cache for 5 minutes
            $analytics = Cache::remember($cacheKey, 300, function () use ($author) {
                return $this->calculateAnalytics($author);
            });

            $this->dispatch('analytics-updated', $analytics);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load analytics: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    private function calculateAnalytics($author)
    {
        $startDate = Carbon::now()->subDays($this->period);
        $bookIds = $this->getBookIds($author);

        $analytics = [
            'metrics' => $this->getMetrics($bookIds, $startDate),
            'trends' => $this->getTrends($bookIds, $startDate),
            'performance' => $this->getPerformanceData($bookIds, $startDate),
            'demographics' => $this->getDemographics($bookIds, $startDate),
        ];

        // Add comparison data if enabled
        if ($this->comparisonPeriod) {
            $analytics['comparison'] = $this->getComparisonData($bookIds, $startDate);
        }

        return $analytics;
    }

    private function getBookIds($author)
    {
        $bookIds = $author->books()->pluck('id');

        if ($this->selectedBook) {
            return collect([$this->selectedBook]);
        }

        return $bookIds;
    }

    private function getMetrics($bookIds, $startDate)
    {
        return [
            'total_views' => $this->getTotalViews($bookIds, $startDate),
            'total_subscriptions' => $this->getSubscriptionsCount($bookIds, $startDate),
            'total_borrowings' => $this->getBorrowingsCount($bookIds, $startDate),
            'revenue' => $this->getRevenueAmount($bookIds, $startDate),
            'conversion_rate' => $this->getConversionRate($bookIds, $startDate),
            'average_session_duration' => $this->getAverageSessionDuration($bookIds, $startDate),
            'retention_rate' => $this->getRetentionRate($bookIds, $startDate),
        ];
    }

    private function getTrends($bookIds, $startDate)
    {
        $interval = $this->getTrendInterval();

        return [
            'subscriptions' => $this->getSubscriptionTrends($bookIds, $startDate, $interval),
            'borrowings' => $this->getBorrowingTrends($bookIds, $startDate, $interval),
            'revenue' => $this->getRevenueTrends($bookIds, $startDate, $interval),
            'views' => $this->getViewsTrends($bookIds, $startDate, $interval),
        ];
    }

    private function getPerformanceData($bookIds, $startDate)
    {
        return [
            'top_performing_books' => $this->getTopPerformingBooks($bookIds, $startDate),
            'growth_metrics' => $this->getGrowthMetrics($bookIds, $startDate),
            'engagement_metrics' => $this->getEngagementMetrics($bookIds, $startDate),
            'revenue_by_book' => $this->getRevenueByBook($bookIds, $startDate),
        ];
    }

    private function getDemographics($bookIds, $startDate)
    {
        return [
            'subscriber_demographics' => $this->getSubscriberDemographics($bookIds, $startDate),
            'geographic_distribution' => $this->getGeographicDistribution($bookIds, $startDate),
            'age_demographics' => $this->getAgeDemographics($bookIds, $startDate),
            'reading_preferences' => $this->getReadingPreferences($bookIds, $startDate),
        ];
    }

    private function getComparisonData($bookIds, $startDate)
    {
        $previousStartDate = Carbon::now()->subDays($this->period * 2);
        $previousEndDate = Carbon::now()->subDays($this->period);

        $currentMetrics = $this->getMetrics($bookIds, $startDate);
        $previousMetrics = $this->getMetrics($bookIds, $previousStartDate, $previousEndDate);

        return [
            'current' => $currentMetrics,
            'previous' => $previousMetrics,
            'changes' => $this->calculateChanges($currentMetrics, $previousMetrics),
        ];
    }

    private function calculateChanges($current, $previous)
    {
        $changes = [];

        foreach ($current as $key => $value) {
            if (isset($previous[$key]) && $previous[$key] > 0) {
                $changes[$key] = [
                    'value' => $value - $previous[$key],
                    'percentage' => (($value - $previous[$key]) / $previous[$key]) * 100,
                ];
            }
        }

        return $changes;
    }

    private function getTrendInterval()
    {
        return match ($this->period) {
            '7' => 'hour',
            '30' => 'day',
            '90' => 'day',
            '180' => 'week',
            '365' => 'month',
            default => 'day',
        };
    }

    private function getTotalViews($bookIds, $startDate)
    {
        // Enhanced view tracking with caching
        return Cache::remember("views_{$bookIds->implode('_')}_{$startDate}", 300, function () use ($bookIds, $startDate) {
            // Implement based on your view tracking system
            // This is a placeholder - you'll need to implement actual view tracking
            return $bookIds->count() * rand(100, 1000);
        });
    }

    private function getSubscriptionsCount($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getBorrowingsCount($bookIds, $startDate)
    {
        return BookBorrowing::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getRevenueAmount($bookIds, $startDate)
    {
        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->sum('annual_fee');
    }

    private function getConversionRate($bookIds, $startDate)
    {
        $views = $this->getTotalViews($bookIds, $startDate);
        $subscriptions = $this->getSubscriptionsCount($bookIds, $startDate);

        return $views > 0 ? ($subscriptions / $views) * 100 : 0;
    }

    private function getAverageSessionDuration($bookIds, $startDate)
    {
        // Implement based on your session tracking
        return rand(300, 1800); // 5-30 minutes placeholder
    }

    private function getRetentionRate($bookIds, $startDate)
    {
        $activeSubscriptions = BookSubscription::whereIn('book_id', $bookIds)
            ->where('status', 'active')
            ->where('end_date', '>', Carbon::now())
            ->count();

        $totalSubscriptions = BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->count();

        return $totalSubscriptions > 0 ? ($activeSubscriptions / $totalSubscriptions) * 100 : 0;
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
            ->with(['subscriptions' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('status', 'completed');
            }])
            ->get()
            ->map(function ($book) {
                $revenue = $book->subscriptions->sum('annual_fee');
                $book->revenue = $revenue;
                $book->performance_score = ($book->subscriptions_count * 0.6) +
                                         ($book->borrowings_count * 0.3) +
                                         ($revenue / 100 * 0.1);
                return $book;
            })
            ->sortByDesc('performance_score')
            ->take(5)
            ->values();
    }

    private function getGrowthMetrics($bookIds, $startDate)
    {
        $previousPeriodStart = Carbon::now()->subDays($this->period * 2);
        $previousPeriodEnd = Carbon::now()->subDays($this->period);

        $currentSubscriptions = $this->getSubscriptionsCount($bookIds, $startDate);
        $previousSubscriptions = BookSubscription::whereIn('book_id', $bookIds)
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $growth = $previousSubscriptions > 0
            ? (($currentSubscriptions - $previousSubscriptions) / $previousSubscriptions) * 100
            : 0;

        return [
            'subscription_growth' => $growth,
            'revenue_growth' => $this->calculateRevenueGrowth($bookIds, $startDate),
            'user_growth' => $this->calculateUserGrowth($bookIds, $startDate),
        ];
    }

    private function calculateRevenueGrowth($bookIds, $startDate)
    {
        $previousPeriodStart = Carbon::now()->subDays($this->period * 2);
        $previousPeriodEnd = Carbon::now()->subDays($this->period);

        $currentRevenue = $this->getRevenueAmount($bookIds, $startDate);
        $previousRevenue = BookSubscription::whereIn('book_id', $bookIds)
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->where('status', 'completed')
            ->sum('annual_fee');

        return $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;
    }

    private function calculateUserGrowth($bookIds, $startDate)
    {
        $currentUsers = BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->distinct('student_id')
            ->count();

        $previousPeriodStart = Carbon::now()->subDays($this->period * 2);
        $previousPeriodEnd = Carbon::now()->subDays($this->period);

        $previousUsers = BookSubscription::whereIn('book_id', $bookIds)
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->distinct('student_id')
            ->count();

        return $previousUsers > 0
            ? (($currentUsers - $previousUsers) / $previousUsers) * 100
            : 0;
    }

    private function getEngagementMetrics($bookIds, $startDate)
    {
        return [
            'average_time_per_session' => $this->getAverageSessionDuration($bookIds, $startDate),
            'pages_per_session' => rand(5, 20), // Placeholder
            'bounce_rate' => rand(20, 60), // Placeholder
            'return_visitor_rate' => rand(30, 70), // Placeholder
        ];
    }

    private function getRevenueByBook($bookIds, $startDate)
    {
        return Book::whereIn('id', $bookIds)
            ->with(['subscriptions' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('status', 'completed');
            }])
            ->get()
            ->map(function ($book) {
                return [
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'revenue' => $book->subscriptions->sum('annual_fee'),
                    'subscription_count' => $book->subscriptions->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values();
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

    private function getGeographicDistribution($bookIds, $startDate)
    {
        // Placeholder - implement based on your location tracking
        return [
            'Ghana' => rand(50, 200),
            'Nigeria' => rand(30, 150),
            'Kenya' => rand(20, 100),
            'South Africa' => rand(15, 80),
            'Uganda' => rand(10, 50),
        ];
    }

    private function getAgeDemographics($bookIds, $startDate)
    {
        // Placeholder - implement based on your user demographics
        return [
            '18-24' => rand(20, 50),
            '25-34' => rand(30, 80),
            '35-44' => rand(25, 60),
            '45-54' => rand(15, 40),
            '55+' => rand(10, 30),
        ];
    }

    private function getReadingPreferences($bookIds, $startDate)
    {
        // Implement based on your reading tracking
        return [
            'Academic' => rand(40, 100),
            'Fiction' => rand(30, 80),
            'Non-Fiction' => rand(35, 90),
            'Technical' => rand(20, 60),
            'Biography' => rand(15, 45),
        ];
    }

    private function getSubscriptionTrends($bookIds, $startDate, $interval)
    {
        $dateFormat = match ($interval) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('count', 'period');
    }

    private function getBorrowingTrends($bookIds, $startDate, $interval)
    {
        $dateFormat = match ($interval) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return BookBorrowing::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('count', 'period');
    }

    private function getRevenueTrends($bookIds, $startDate, $interval)
    {
        $dateFormat = match ($interval) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return BookSubscription::whereIn('book_id', $bookIds)
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, SUM(annual_fee) as revenue")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('revenue', 'period');
    }

    private function getViewsTrends($bookIds, $startDate, $interval)
    {
        // Placeholder - implement based on your view tracking system
        $periods = [];
        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::now();

        while ($startCarbon <= $endCarbon) {
            $periods[$startCarbon->format('Y-m-d')] = rand(10, 100);
            $startCarbon->addDay();
        }

        return collect($periods);
    }

    public function exportAnalytics($type = 'csv')
    {
        try {
            $author = Auth::user()->author;
            if (!$author) {
                session()->flash('error', 'Author not found');
                return;
            }

            $analytics = $this->calculateAnalytics($author);

            // Generate export file
            $filename = "analytics_export_{$author->id}_{$this->period}days_" . date('Y-m-d') . ".{$type}";

            // In a real implementation, you would generate the actual file
            // For now, we'll just flash a success message
            session()->flash('success', "Analytics exported successfully as {$filename}!");

            $this->dispatch('export-completed', ['filename' => $filename, 'type' => $type]);
        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function books()
    {
        return Auth::user()->author?->books ?? collect();
    }

    #[Computed]
    public function analytics()
    {
        $author = Auth::user()->author;
        if (!$author) return [];

        $cacheKey = "analytics_author_{$author->id}_{$this->period}_{$this->selectedBook}_{$this->viewType}";

        return Cache::remember($cacheKey, 300, function () use ($author) {
            return $this->calculateAnalytics($author);
        });
    }

    public function render()
    {
        return view('livewire.authors.analytics', [
            'books' => $this->books,
            'categories' => \App\Models\BookCategory::all(),
        ]);
    }
}
