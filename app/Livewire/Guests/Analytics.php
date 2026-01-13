<?php

namespace App\Livewire\Guests;

use App\Models\Assessment;
use App\Models\BookSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Analytics extends Component
{
    public $timeframe = 'month'; // 'week', 'month', 'quarter', 'year'
    public $analyticsData = [];
    public $performanceMetrics = [];
    public $learningInsights = [];
    public $goals = [];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $user = Auth::user();

        if (!$user->student) {
            return;
        }

        $cacheKey = "guests_analytics_{$user->id}_{$this->timeframe}";

        $this->analyticsData = Cache::remember($cacheKey, 300, function () use ($user) {
            return $this->calculateAnalytics($user);
        });
    }

    private function calculateAnalytics($user)
    {
        $startDate = $this->getStartDate();

        // Assessment performance
        $assessments = Assessment::where('student_id', $user->student->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        // Reading activity
        $subscriptions = BookSubscription::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->with('book')
            ->get();

        // Performance metrics
        $this->performanceMetrics = [
            'assessments_taken' => $assessments->count(),
            'average_score' => round($assessments->avg('score') ?? 0, 1),
            'improvement_rate' => $this->calculateImprovementRate($assessments),
            'study_consistency' => $this->calculateStudyConsistency($assessments),
            'books_subscribed' => $subscriptions->count(),
            'reading_progress' => $this->calculateReadingProgress($subscriptions),
        ];

        // Learning insights
        $this->learningInsights = [
            'strongest_subjects' => $this->getStrongestSubjects($assessments),
            'areas_for_improvement' => $this->getWeakestSubjects($assessments),
            'study_patterns' => $this->getStudyPatterns($assessments),
            'recommended_actions' => $this->getRecommendedActions(),
        ];

        // Goals and achievements
        $this->goals = [
            'weekly_assessment_goal' => [
                'target' => 3,
                'current' => $this->getWeeklyAssessmentCount(),
                'progress' => min(100, ($this->getWeeklyAssessmentCount() / 3) * 100)
            ],
            'reading_goal' => [
                'target' => 5,
                'current' => $subscriptions->count(),
                'progress' => min(100, ($subscriptions->count() / 5) * 100)
            ],
            'score_improvement_goal' => [
                'target' => 85,
                'current' => $this->performanceMetrics['average_score'],
                'progress' => min(100, ($this->performanceMetrics['average_score'] / 85) * 100)
            ]
        ];

        return [
            'performance_trend' => $this->getPerformanceTrend($assessments),
            'activity_heatmap' => $this->getActivityHeatmap($assessments),
            'subject_breakdown' => $this->getSubjectBreakdown($assessments),
            'time_spent_learning' => $this->calculateTimeSpentLearning($assessments),
        ];
    }

    private function getStartDate()
    {
        return match ($this->timeframe) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function calculateImprovementRate($assessments)
    {
        if ($assessments->count() < 2) return 0;

        $recent = $assessments->sortBy('created_at')->take(-5)->avg('score');
        $older = $assessments->sortBy('created_at')->take(5)->avg('score');

        return $older > 0 ? round((($recent - $older) / $older) * 100, 1) : 0;
    }

    private function calculateStudyConsistency($assessments)
    {
        $days = $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('Y-m-d');
        });

        $totalDays = $this->getStartDate()->diffInDays(now()) + 1;
        return round(($days->count() / $totalDays) * 100, 1);
    }

    private function calculateReadingProgress($subscriptions)
    {
        // Simple metric: assume each subscription represents some reading progress
        return $subscriptions->count() * 15; // 15% progress per book (rough estimate)
    }

    private function getStrongestSubjects($assessments)
    {
        return $assessments->where('score', '>', 80)
            ->groupBy('subject_id')
            ->map(function ($group) {
                return [
                    'subject' => $group->first()->subject->title ?? 'General',
                    'average_score' => round($group->avg('score'), 1),
                    'attempts' => $group->count()
                ];
            })
            ->sortByDesc('average_score')
            ->take(3)
            ->values();
    }

    private function getWeakestSubjects($assessments)
    {
        return $assessments->where('score', '<', 70)
            ->groupBy('subject_id')
            ->map(function ($group) {
                return [
                    'subject' => $group->first()->subject->title ?? 'General',
                    'average_score' => round($group->avg('score'), 1),
                    'attempts' => $group->count()
                ];
            })
            ->sortBy('average_score')
            ->take(3)
            ->values();
    }

    private function getStudyPatterns($assessments)
    {
        $hourlyDistribution = $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('H');
        })->map->count();

        $peakHour = $hourlyDistribution->keys()->first();

        return [
            'peak_study_hour' => $peakHour ? $peakHour . ':00' : 'N/A',
            'most_active_day' => $this->getMostActiveDay($assessments),
            'average_session_length' => 25 // minutes (estimated)
        ];
    }

    private function getMostActiveDay($assessments)
    {
        $dayDistribution = $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('l');
        })->map->count();

        return $dayDistribution->keys()->first() ?? 'N/A';
    }

    private function getRecommendedActions()
    {
        $actions = [];

        if ($this->performanceMetrics['average_score'] < 70) {
            $actions[] = 'Focus on improving weak subjects through targeted practice';
        }

        if ($this->performanceMetrics['study_consistency'] < 50) {
            $actions[] = 'Try to study more consistently - aim for daily practice sessions';
        }

        if ($this->performanceMetrics['assessments_taken'] < 5) {
            $actions[] = 'Take more self-assessments to track your progress better';
        }

        return $actions ?: ['Keep up the great work! Your learning is on track.'];
    }

    private function getWeeklyAssessmentCount()
    {
        $user = Auth::user();
        return Assessment::where('student_id', $user->student->id ?? 0)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();
    }

    private function getPerformanceTrend($assessments)
    {
        return $assessments->groupBy(function ($assessment) {
            return $assessment->created_at->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => $group->first()->created_at->format('M d'),
                'average_score' => round($group->avg('score'), 1),
                'count' => $group->count()
            ];
        })->values();
    }

    private function getActivityHeatmap($assessments)
    {
        $heatmap = [];
        $startDate = $this->getStartDate();

        for ($date = $startDate->copy(); $date->lte(now()); $date->addDay()) {
            $dayAssessments = $assessments->filter(function ($assessment) use ($date) {
                return $assessment->created_at->isSameDay($date);
            });

            $heatmap[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $dayAssessments->count(),
                'intensity' => min(4, $dayAssessments->count()) // 0-4 scale for heatmap colors
            ];
        }

        return $heatmap;
    }

    private function getSubjectBreakdown($assessments)
    {
        return $assessments->groupBy('subject_id')
            ->map(function ($group) {
                return [
                    'subject' => $group->first()->subject->title ?? 'General',
                    'count' => $group->count(),
                    'average_score' => round($group->avg('score'), 1)
                ];
            })
            ->values();
    }

    private function calculateTimeSpentLearning($assessments)
    {
        // Estimate based on assessment count (rough calculation)
        return $assessments->count() * 30; // 30 minutes per assessment session
    }

    public function updatedTimeframe()
    {
        $this->loadAnalytics();
    }

    public function render()
    {
        return view('livewire.guests.analytics');
    }
}
