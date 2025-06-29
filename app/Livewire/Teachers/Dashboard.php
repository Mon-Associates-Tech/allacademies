<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $selectedTab = 'overview';
    public $searchTerm = '';

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $teacher = auth()->user()->teacher;

        // Load teacher's academic groups with levels and subjects
        $teacher->load([
            'academicGroups.academicLevels.academicSubjects',
            'academicLevels.academicSubjects',
            'subjects'
        ]);

        // Get recent assignments
        $recentAssignments = Assignment::where('teacher_id', $teacher->id)
            ->with(['academicSubject', 'academicGroups', 'academicLevels'])
            ->latest()
            ->take(5)
            ->get();

        // Get pending submissions
        $pendingSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->where('status', 'submitted')
        ->with(['assignment', 'student.user'])
        ->latest()
        ->take(10)
        ->get();

        // Statistics
        $stats = [
            'total_assignments' => Assignment::where('teacher_id', $teacher->id)->count(),
            'active_assignments' => Assignment::where('teacher_id', $teacher->id)
                ->where('status', 'published')
                ->where('ends_at', '>', now())
                ->count(),
            'pending_grades' => AssignmentSubmission::whereHas('assignment', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->where('status', 'submitted')->count(),
            'total_students' => $teacher->assignedStudents()->count(),
        ];

        return view('livewire.teachers.dashboard', [
            'teacher' => $teacher,
            'recentAssignments' => $recentAssignments,
            'pendingSubmissions' => $pendingSubmissions,
            'stats' => $stats,
        ]);
    }
}
