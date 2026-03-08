<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Classroom\VirtualSession;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Teacher;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    #[Url]
    public $activeTab = 'dashboard';

    #[Url]
    public $selectedPeriod = 'month';

    public $teacher;

    public $currentTeam;

    public $totalStudents = 0;

    public $totalAssignments = 0;

    public $totalSubjects = 0;

    public $recentAssignments = [];

    public $upcomingAssignments = [];

    public $myStudents = [];

    public $mySubjects = [];

    public $myAcademicLevels = [];

    public bool $showQuickActions = true;

    protected $listeners = ['teacherTabChanged' => 'setActiveTab'];

    public function mount(): void
    {
        if (! $this->activeTab) {
            $this->activeTab = 'dashboard';
        }

        $this->loadTeacherData();
        $this->loadDashboardMetrics();
    }

    private function loadTeacherData(): void
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        $this->currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if (! $this->currentTeam) {
            $this->currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }
    }

    private function loadDashboardMetrics(): void
    {
        if (! $this->teacher) {
            return;
        }

        // Get total counts using new helper methods
        $this->totalStudents = $this->teacher->getStudentsCount();
        $this->totalAssignments = $this->teacher->assignments()->count();
        $this->totalSubjects = $this->teacher->academicSubjects()->count();

        // Get recent assignments (last 10)
        $this->recentAssignments = $this->teacher->assignments()
            ->with(['academicSubject', 'students', 'academicLevels'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->toArray();

        // Get upcoming assignments (due in next 7 days)
        $this->upcomingAssignments = $this->teacher->assignments()
            ->with(['academicSubject', 'students', 'academicLevels'])
            ->where('ends_at', '>=', now())
            ->where('ends_at', '<=', now()->addDays(7))
            ->orderBy('ends_at', 'asc')
            ->get()
            ->toArray();

        $allStudents = $this->teacher->getAllStudents();
        $this->myStudents = $allStudents->take(20)->map(function ($student) {
            return [
                'id' => $student->id,
                'user' => $student->user->toArray(),
                'academic_level' => $student->academicLevel ? $student->academicLevel->toArray() : null,
                'academic_group' => $student->academicLevel && $student->academicLevel->academicGroup
                    ? $student->academicLevel->academicGroup->toArray()
                    : null,
            ];
        })->toArray();

        // Get my subjects
        $this->mySubjects = $this->teacher->academicSubjects()
            ->with(['academicLevel.academicGroup'])
            ->get()
            ->toArray();

        // Get my academic levels with enhanced student count
        $this->myAcademicLevels = $this->teacher->academicLevels()
            ->with(['academicGroup'])
            ->get()
            ->map(function ($level) {
                $levelArray = $level->toArray();
                // Get actual count of students this teacher has access to in this level
                $levelArray['accessible_students_count'] = $this->teacher->getStudentsByLevel($level->id)->count();

                return $levelArray;
            })
            ->toArray();
    }

    /**
     * Get attendance statistics for the teacher
     */
    public function attendanceStats(): array
    {
        if (! $this->teacher) {
            return [
                'total_records' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'attendance_rate' => 0,
                'records_this_period' => 0,
            ];
        }

        $startDate = $this->getPeriodStartDate();

        // Get attendance IDs for this teacher
        $attendanceIds = Attendance::where('teacher_id', $this->teacher->id)->pluck('id');

        // Query AttendanceRecord for status-based counts (status is in attendance_records table)
        $recordsQuery = AttendanceRecord::whereIn('attendance_id', $attendanceIds);

        $totalRecords = (clone $recordsQuery)->count();
        $presentCount = (clone $recordsQuery)->where('status', 'present')->count();
        $absentCount = (clone $recordsQuery)->where('status', 'absent')->count();
        $lateCount = (clone $recordsQuery)->where('status', 'late')->count();

        // Records this period - join with attendances to filter by date
        $recordsThisPeriod = AttendanceRecord::whereHas('attendance', function ($query) use ($startDate) {
            $query->where('teacher_id', $this->teacher->id)
                ->where('date', '>=', $startDate);
        })->count();

        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;

        return [
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'attendance_rate' => $attendanceRate,
            'records_this_period' => $recordsThisPeriod,
        ];
    }

    /**
     * Get message statistics for the teacher
     */
    public function messageStats(): array
    {
        $startDate = $this->getPeriodStartDate();

        $sentMessages = Message::where('sender_id', Auth::id())->count();
        $sentThisPeriod = Message::where('sender_id', Auth::id())
            ->where('created_at', '>=', $startDate)
            ->count();
        // Recipients are stored in message_recipients table, not as recipient_id in messages
        $unreadMessages = MessageRecipient::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return [
            'sent_messages' => $sentMessages,
            'sent_this_period' => $sentThisPeriod,
            'unread_messages' => $unreadMessages,
        ];
    }

    /**
     * Get virtual classroom statistics
     */
    public function virtualClassroomStats(): array
    {
        if (! $this->teacher) {
            return [
                'total_sessions' => 0,
                'upcoming_sessions' => 0,
                'completed_sessions' => 0,
                'total_participants' => 0,
            ];
        }

        $totalSessions = VirtualSession::where('teacher_id', $this->teacher->id)->count();
        $upcomingSessions = VirtualSession::where('teacher_id', $this->teacher->id)
            ->where('scheduled_start', '>', now())
            ->count();
        $completedSessions = VirtualSession::where('teacher_id', $this->teacher->id)
            ->where('status', 'completed')
            ->count();

        // Get total participants across all sessions
        $totalParticipants = VirtualSession::where('teacher_id', $this->teacher->id)
            ->withCount('participants')
            ->get()
            ->sum('participants_count');

        return [
            'total_sessions' => $totalSessions,
            'upcoming_sessions' => $upcomingSessions,
            'completed_sessions' => $completedSessions,
            'total_participants' => $totalParticipants,
        ];
    }

    /**
     * Get assignment submission statistics
     */
    public function submissionStats(): array
    {
        if (! $this->teacher) {
            return [
                'total_submissions' => 0,
                'pending_review' => 0,
                'graded' => 0,
                'in_progress' => 0,
                'not_started' => 0,
                'average_score' => 0,
                'submission_rate' => 0,
            ];
        }

        $assignmentIds = $this->teacher->assignments()->pluck('id');

        $totalSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->count();

        // Count by status column: 'not_started', 'in_progress', 'submitted', 'graded'
        $pendingReview = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->count();
        $graded = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'graded')
            ->count();
        $inProgress = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'in_progress')
            ->count();
        $notStarted = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'not_started')
            ->count();

        // Calculate average score from graded submissions (using score and total_marks)
        $gradedSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->where('total_marks', '>', 0)
            ->get();

        $averageScore = 0;
        if ($gradedSubmissions->count() > 0) {
            $averageScore = $gradedSubmissions->avg(function ($submission) {
                return ($submission->score / $submission->total_marks) * 100;
            });
        }

        // Calculate submission rate (submitted + graded vs total expected)
        $totalExpectedSubmissions = $this->teacher->assignments()
            ->withCount('students')
            ->get()
            ->sum('students_count');
        $completedSubmissions = $pendingReview + $graded; // submitted or graded
        $submissionRate = $totalExpectedSubmissions > 0
            ? round(($completedSubmissions / $totalExpectedSubmissions) * 100, 1)
            : 0;

        return [
            'total_submissions' => $totalSubmissions,
            'pending_review' => $pendingReview,
            'graded' => $graded,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
            'average_score' => round($averageScore, 1),
            'submission_rate' => $submissionRate,
        ];
    }

    /**
     * Get student distribution by academic level for chart
     */
    public function studentDistributionChartData(): array
    {
        if (! $this->teacher) {
            return [
                'labels' => [],
                'data' => [],
                'colors' => [],
            ];
        }

        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
        ];

        $distribution = [];
        $levels = $this->teacher->academicLevels()->with('academicGroup')->get();

        foreach ($levels as $index => $level) {
            $studentCount = $this->teacher->getStudentsByLevel($level->id)->count();
            if ($studentCount > 0) {
                $distribution[] = [
                    'label' => $level->name,
                    'count' => $studentCount,
                    'color' => $colors[$index % count($colors)],
                ];
            }
        }

        return [
            'labels' => collect($distribution)->pluck('label')->toArray(),
            'data' => collect($distribution)->pluck('count')->toArray(),
            'colors' => collect($distribution)->pluck('color')->toArray(),
        ];
    }

    /**
     * Get assignment completion trend data for chart
     */
    public function assignmentTrendChartData(): array
    {
        if (! $this->teacher) {
            return [
                'labels' => [],
                'created' => [],
                'completed' => [],
            ];
        }

        $startDate = $this->getPeriodStartDate();
        $labels = [];
        $created = [];
        $completed = [];

        // Generate data points based on period
        $interval = $this->selectedPeriod === 'year' ? 'month' : ($this->selectedPeriod === 'month' ? 'day' : 'day');
        $format = $this->selectedPeriod === 'year' ? 'M' : 'd M';
        $days = $this->selectedPeriod === 'year' ? 12 : ($this->selectedPeriod === 'month' ? 30 : 7);

        for ($i = $days - 1; $i >= 0; $i--) {
            if ($this->selectedPeriod === 'year') {
                $date = now()->subMonths($i);
                $startOfPeriod = $date->copy()->startOfMonth();
                $endOfPeriod = $date->copy()->endOfMonth();
            } else {
                $date = now()->subDays($i);
                $startOfPeriod = $date->copy()->startOfDay();
                $endOfPeriod = $date->copy()->endOfDay();
            }

            $labels[] = $date->format($format);

            $created[] = $this->teacher->assignments()
                ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
                ->count();

            $assignmentIds = $this->teacher->assignments()->pluck('id');
            $completed[] = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->whereBetween('submitted_at', [$startOfPeriod, $endOfPeriod])
                ->count();
        }

        return [
            'labels' => $labels,
            'created' => $created,
            'completed' => $completed,
        ];
    }

    /**
     * Get quick action items for the teacher
     */
    public function quickActionItems(): array
    {
        $actions = [];
        $submissionStats = $this->submissionStats();
        $messageStats = $this->messageStats();
        $virtualStats = $this->virtualClassroomStats();

        $actions[] = [
            'title' => 'Create Assignment',
            'description' => 'Create a new assignment for your students',
            'route' => 'teachers.assignments.create',
            'icon' => 'clipboard-list',
            'badge' => 0,
        ];

        $actions[] = [
            'title' => 'Take Attendance',
            'description' => 'Record attendance for your classes',
            'route' => 'teachers.attendance.take',
            'icon' => 'check-circle',
            'badge' => 0,
        ];

        if ($submissionStats['pending_review'] > 0) {
            $actions[] = [
                'title' => 'Review Submissions',
                'description' => 'Grade pending assignment submissions',
                'route' => 'teachers.assignments.index',
                'icon' => 'academic-cap',
                'badge' => $submissionStats['pending_review'],
            ];
        }

        $actions[] = [
            'title' => 'Send Message',
            'description' => 'Communicate with your students',
            'route' => 'teachers.messages.compose',
            'icon' => 'mail',
            'badge' => $messageStats['unread_messages'],
        ];

        $actions[] = [
            'title' => 'Schedule Class',
            'description' => 'Create a virtual classroom session',
            'route' => 'teachers.classroom.create',
            'icon' => 'video-camera',
            'badge' => $virtualStats['upcoming_sessions'],
        ];

        $actions[] = [
            'title' => 'View Students',
            'description' => 'Manage and view your students',
            'route' => 'teachers.students.index',
            'icon' => 'user-group',
            'badge' => 0,
        ];

        $actions[] = [
            'title' => 'My Subjects',
            'description' => 'View your assigned subjects',
            'route' => 'teachers.subjects.index',
            'icon' => 'book-open',
            'badge' => 0,
        ];

        $actions[] = [
            'title' => 'View Schedule',
            'description' => 'Check your teaching schedule',
            'route' => 'teachers.schedules',
            'icon' => 'calendar',
            'badge' => 0,
        ];

        return $actions;
    }

    /**
     * Get recent activity for the teacher
     */
    public function recentActivity(): array
    {
        if (! $this->teacher) {
            return [
                'recent_submissions' => collect(),
                'recent_attendance' => collect(),
            ];
        }

        $assignmentIds = $this->teacher->assignments()->pluck('id');

        $recentSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->with(['student.user', 'assignment'])
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        $recentAttendance = Attendance::where('teacher_id', $this->teacher->id)
            ->with(['student.user', 'academicLevel'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return [
            'recent_submissions' => $recentSubmissions,
            'recent_attendance' => $recentAttendance,
        ];
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics(): array
    {
        $submissionStats = $this->submissionStats();
        $attendanceStats = $this->attendanceStats();

        return [
            'average_score' => $submissionStats['average_score'],
            'submission_rate' => $submissionStats['submission_rate'],
            'attendance_rate' => $attendanceStats['attendance_rate'],
            'assignments_created' => $this->totalAssignments,
        ];
    }

    /**
     * Get the start date based on selected period
     */
    private function getPeriodStartDate(): Carbon
    {
        return match ($this->selectedPeriod) {
            'today' => now()->startOfDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function refreshDashboard(): void
    {
        $this->loadDashboardMetrics();
        $this->dispatch('dashboardRefreshed');
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.teachers.dashboard', [
            'teacher' => $this->teacher,
            'currentTeam' => $this->currentTeam,
            'totalStudents' => $this->totalStudents,
            'totalAssignments' => $this->totalAssignments,
            'totalSubjects' => $this->totalSubjects,
            'recentAssignments' => $this->recentAssignments,
            'upcomingAssignments' => $this->upcomingAssignments,
            'myStudents' => $this->myStudents,
            'mySubjects' => $this->mySubjects,
            'myAcademicLevels' => $this->myAcademicLevels,
        ]);
    }
}
