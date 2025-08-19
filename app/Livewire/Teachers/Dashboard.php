<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use App\Models\Team;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    #[Url]
    public $activeTab = 'dashboard';

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

    protected $listeners = ['teacherTabChanged' => 'setActiveTab'];

    public function mount(): void
    {
        if (!$this->activeTab) {
            $this->activeTab = 'dashboard';
        }

        $this->loadTeacherData();
        $this->loadDashboardMetrics();
    }

    private function loadTeacherData(): void
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();

        $this->currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if (!$this->currentTeam) {
            $this->currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }
    }

    private function loadDashboardMetrics(): void
    {
        if (!$this->teacher) {
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
            ->take(10)
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
