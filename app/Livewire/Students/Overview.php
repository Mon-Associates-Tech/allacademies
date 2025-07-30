<?php

namespace App\Livewire\Students;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\AcademicSubject as Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class Overview extends Component
{
    #[Url]
    public $activeTab = 'overview';

    protected $listeners = ['studentTabChanged' => 'setActiveTab'];

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function mount(): void
    {
        if(!$this->activeTab){
            $this->activeTab = 'overview';
        }
    }

    private function getTimeBasedGreeting(): string
    {
        $hour = Carbon::now()->hour;

        if ($hour < 12) {
            return 'Good morning';
        } elseif ($hour < 17) {
            return 'Good afternoon';
        } else {
            return 'Good evening';
        }
    }

    private function getStudyStreak($student): int
    {
        // Calculate consecutive days with assessment activity
        $streak = 0;
        $currentDate = Carbon::today();

        while ($currentDate->greaterThan(Carbon::today()->subDays(30))) {
            $hasActivity = Assessment::where('student_id', $student->id)
                ->whereDate('created_at', $currentDate)
                ->exists();

            if ($hasActivity) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getRecentAchievements($student): array
    {
        $achievements = [];

        // Check for perfect scores in last 7 days
        $perfectScores = Assessment::where('student_id', $student->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('score', '>=', 90)
            ->count();

        if ($perfectScores > 0) {
            $achievements[] = [
                'type' => 'perfect_score',
                'message' => "🎯 {$perfectScores} excellent score(s) this week!",
                'color' => 'text-green-600'
            ];
        }

        // Check for consistency (assessments on multiple days)
        $activeDays = Assessment::where('student_id', $student->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date')
            ->groupBy('created_at')
            ->count();

        if ($activeDays >= 5) {
            $achievements[] = [
                'type' => 'consistency',
                'message' => "🔥 Great consistency - active {$activeDays} days this week!",
                'color' => 'text-blue-600'
            ];
        }

        return $achievements;
    }

    private function getPerformanceTrend($student): array
    {
        $thisWeekAvg = Assessment::where('student_id', $student->id)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $lastWeekAvg = Assessment::where('student_id', $student->id)
            ->whereBetween('created_at', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $difference = $thisWeekAvg - $lastWeekAvg;

        return [
            'current' => round($thisWeekAvg, 1),
            'previous' => round($lastWeekAvg, 1),
            'difference' => round($difference, 1),
            'trend' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'stable')
        ];
    }

    private function getQuickStats($student): array
    {
        $todayAssessments = Assessment::where('student_id', $student->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $weeklyAssessments = Assessment::where('student_id', $student->id)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        $pendingActivities = Activity::forStudent($student->id)
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(3))
            ->count();

        return [
            'today_assessments' => $todayAssessments,
            'weekly_assessments' => $weeklyAssessments,
            'pending_activities' => $pendingActivities,
            'study_streak' => $this->getStudyStreak($student)
        ];
    }

    private function getSubjectProgress($student): array
    {
        return Subject::whereIn('id', function($query) use ($student) {
            $query->select('subject_id')
                ->from('assessments')
                ->where('student_id', $student->id)
                ->whereNotNull('subject_id');
        })
        ->get()
        ->map(function($subject) use ($student) {
            $assessments = Assessment::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('status', 'completed');

            $totalAssessments = $assessments->count();
            $averageScore = $assessments->avg('score') ?? 0;
            $recentAssessments = $assessments->where('created_at', '>=', Carbon::now()->subDays(7))->count();

            // Calculate progress (simplified - based on number of completed assessments)
            $targetAssessments = 20; // Could be configurable
            $progress = min(($totalAssessments / $targetAssessments) * 100, 100);

            return [
                'name' => $subject->name,
                'average_score' => round($averageScore, 1),
                'total_assessments' => $totalAssessments,
                'recent_activity' => $recentAssessments,
                'progress_percentage' => round($progress, 1),
                'color' => $this->getSubjectColor($subject->name)
            ];
        })
        ->sortByDesc('recent_activity')
        ->take(5)
        ->values()
        ->toArray();
    }

    private function getSubjectColor($subjectName): string
    {
        $colors = [
            'Mathematics' => 'blue',
            'Science' => 'green',
            'English' => 'purple',
            'History' => 'yellow',
            'Geography' => 'indigo',
        ];

        return $colors[$subjectName] ?? 'gray';
    }

    public function render()
    {
        $student = auth()->user()->student;
        if(!$student) return;

        // Existing data
        $bookSubscriptions = BookSubscription::whereHas('user', function($query) use ($student) {
            $query->where('user_id', auth()->user()->id);
        })->latest()->take(5)->get();

        $bookCount = Book::whereHas('students', function($query) use ($student) {
            $query->where('student_id', $student->id);
        })->count();

        $recentAssessments = Assessment::where('student_id', $student->id)
            ->with(['subject', 'topic'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingActivities = Activity::forStudent($student->id)
            ->upcoming()
            ->with(['subject', 'group'])
            ->take(5)
            ->get();

        $upcomingActivitiesCount = Activity::forStudent($student->id)
            ->upcoming()
            ->count();

        $overallScore = Assessment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $overallScore = round($overallScore, 1);

        $subjectPerformance = Assessment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->select('subject_id', DB::raw('AVG(score) as average_score'))
            ->groupBy('subject_id')
            ->with('subject')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->subject->name,
                    'score' => round($item->average_score, 1)
                ];
            });

        // New enhanced data
        $greeting = $this->getTimeBasedGreeting();
        $achievements = $this->getRecentAchievements($student);
        $performanceTrend = $this->getPerformanceTrend($student);
        $quickStats = $this->getQuickStats($student);
        $subjectProgress = $this->getSubjectProgress($student);

        return view('livewire.students.overview', [
            'bookSubscriptions' => $bookSubscriptions,
            'bookCount' => $bookCount,
            'recentAssessments' => $recentAssessments,
            'upcomingActivities' => $upcomingActivities,
            'upcomingActivitiesCount' => $upcomingActivitiesCount,
            'overallScore' => $overallScore,
            'subjectPerformance' => $subjectPerformance,
            // Enhanced data
            'greeting' => $greeting,
            'achievements' => $achievements,
            'performanceTrend' => $performanceTrend,
            'quickStats' => $quickStats,
            'subjectProgress' => $subjectProgress,
        ]);
    }
}
