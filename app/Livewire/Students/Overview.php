<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject as Subject;
use App\Models\Activity;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
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

    #[Computed]
    public function accountAlerts()
    {
        $alerts = [];
        $student = auth()->user()->student;

        if (!$student) {
            return $alerts;
        }

        // Check for missing academic group
        if (!$student->academic_group_id) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'No Academic Group Assigned',
                'message' => 'You are not assigned to any academic group. This may limit your access to resources and activities.',
                'icon' => '👥',
                'priority' => 1,
                'action' => 'Contact Administrator',
                'actionType' => 'admin_contact'
            ];
        }

        // Check for missing academic level
        if (!$student->academic_level_id) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'No Academic Level Assigned',
                'message' => 'You are not assigned to any academic level. This affects subject availability and learning materials.',
                'icon' => '📚',
                'priority' => 1,
                'action' => 'Contact Administrator',
                'actionType' => 'admin_contact'
            ];
        }

        // Check for missing both (critical)
        if (!$student->academic_group_id && !$student->academic_level_id) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Account Setup Incomplete',
                'message' => 'Your account is missing critical academic assignments. Please contact your administrator immediately to complete your profile setup.',
                'icon' => '⚠️',
                'priority' => 0, // Highest priority
                'action' => 'Contact Administrator Urgently',
                'actionType' => 'urgent_admin_contact'
            ];
        }

        // Check if student has no accessible subjects
        $accessibleSubjects = $student->getAllAccessibleSubjects();
        if ($accessibleSubjects->isEmpty()) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'No Academic Subjects Available',
                'message' => 'You currently have no academic subjects assigned. This may affect your ability to take assessments and access learning materials.',
                'icon' => '📖',
                'priority' => 2,
                'action' => 'View Subject Details',
                'actionType' => 'subjects_tab'
            ];
        }

        // Check if student has no primary teacher
        if (!$student->primaryTeacher()) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'No Primary Teacher Assigned',
                'message' => 'You don\'t have a primary teacher assigned. A primary teacher helps guide your learning journey.',
                'icon' => '👨‍🏫',
                'priority' => 3,
                'action' => 'View Teachers',
                'actionType' => 'teachers_info'
            ];
        }

        // Check if student has no active library card
        if (!$student->activeLibraryCard) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'No Active Library Card',
                'message' => 'You don\'t have an active library card. This limits your ability to borrow books and access library resources.',
                'icon' => '📚',
                'priority' => 4,
                'action' => 'Request Library Card',
                'actionType' => 'library_card_request'
            ];
        }

        // Check for incomplete profile information
        $user = auth()->user();
        $incompleteFields = [];

        if (!$user->email_verified_at) {
            $incompleteFields[] = 'Email verification';
        }

        if (!$student->school_id) {
            $incompleteFields[] = 'School assignment';
        }

        if (!empty($incompleteFields)) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Profile Incomplete',
                'message' => 'Your profile is missing: ' . implode(', ', $incompleteFields) . '. Complete your profile for full access.',
                'icon' => '👤',
                'priority' => 5,
                'action' => 'Complete Profile',
                'actionType' => 'profile_completion'
            ];
        }

        // Sort alerts by priority (lower number = higher priority)
        usort($alerts, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $alerts;
    }

    #[Computed]
    public function accountCompleteness()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return ['percentage' => 0, 'missing' => []];
        }

        $checklist = [
            'academic_group' => [
                'label' => 'Academic Group',
                'completed' => !is_null($student->academic_group_id),
                'weight' => 20
            ],
            'academic_level' => [
                'label' => 'Academic Level',
                'completed' => !is_null($student->academic_level_id),
                'weight' => 20
            ],
            'school_assignment' => [
                'label' => 'School Assignment',
                'completed' => !is_null($student->school_id),
                'weight' => 15
            ],
            'email_verified' => [
                'label' => 'Email Verified',
                'completed' => !is_null(auth()->user()->email_verified_at),
                'weight' => 10
            ],
            'library_card' => [
                'label' => 'Active Library Card',
                'completed' => !is_null($student->activeLibraryCard),
                'weight' => 10
            ],
            'primary_teacher' => [
                'label' => 'Primary Teacher',
                'completed' => !is_null($student->primaryTeacher()),
                'weight' => 10
            ],
            'accessible_subjects' => [
                'label' => 'Available Subjects',
                'completed' => $student->getAllAccessibleSubjects()->isNotEmpty(),
                'weight' => 15
            ]
        ];

        $totalWeight = array_sum(array_column($checklist, 'weight'));
        $completedWeight = array_sum(array_map(function($item) {
            return $item['completed'] ? $item['weight'] : 0;
        }, $checklist));

        $percentage = $totalWeight > 0 ? round(($completedWeight / $totalWeight) * 100) : 0;
        $missing = array_filter($checklist, function($item) {
            return !$item['completed'];
        });

        return [
            'percentage' => $percentage,
            'checklist' => $checklist,
            'missing' => $missing,
            'total_items' => count($checklist),
            'completed_items' => count(array_filter($checklist, function($item) {
                return $item['completed'];
            }))
        ];
    }

    #[Computed]
    public function academicStatus()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return null;
        }

        if(!$student->user->currentTeam){
           $currentTeam = $student->user->currentTeam()->create([
                'name' => $student->user->name . ' School',
                'owner_id' => $student->user->id
            ]);
            $student->user->current_team_id  = $currentTeam->id;
            $student->user->save();

        }

        $status = [
            'academic_group' => $student->academicGroup ? [
                'name' => $student->academicGroup->name,
                'id' => $student->academicGroup->id,
                'teachers_count' => $student->academicGroup->teachers()->count(),
                'primary_teachers_count' => $student->academicGroup->primaryTeachers()->count()
            ] : null,
            'academic_level' => $student->academicLevel ? [
                'name' => $student->academicLevel->name,
                'label' => $student->academicLevel->label,
                'id' => $student->academicLevel->id,
                'subjects_count' => $student->academicLevel->academicSubjects()->count()
            ] : null,
            'school' => $student->school ? [
                'name' => $student->school->name,
                'id' => $student->school->id
            ] : null,
            'subjects_summary' => [
                'total_accessible' => $student->getAllAccessibleSubjects()->count(),
                'from_level' => $student->academicLevel ? $student->academicLevel->academicSubjects()->count() : 0,
                'individually_assigned' => $student->getIndividuallyAssignedSubjects()->count(),
                'removed_from_level' => $student->getRemovedLevelSubjects()->count()
            ]
        ];

        return $status;
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
        $user = auth()->user();
        if (!$user){
            return;
        }
        $student = $user->student;
        if(!$student) return view('livewire.students.overview-no-student');

        // Existing data
        $bookSubscriptions = BookSubscription::whereHas('user', function($query) use ($student) {
            $query->where('user_id', auth()->user()->id);
        })->latest()->take(5)->get();

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
            'bookCount' => 0,
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
            // New comprehensive data
            'accountAlerts' => $this->accountAlerts,
            'accountCompleteness' => $this->accountCompleteness,
            'academicStatus' => $this->academicStatus,
        ]);
    }
}
