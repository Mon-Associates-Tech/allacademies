<?php

namespace App\Livewire\Teachers;

use App\Models\User;
use Livewire\Component;

class PublicProfile extends Component
{
    public User $user;
    public $teacher;
    public $totalStudents = 0;
    public $totalAssignments = 0;
    public $totalSubjects = 0;
    public $recentAssignments;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->teacher = $user->teacher;

        if (!$this->teacher) {
            abort(404, 'Teacher profile not found.');
        }

        $this->loadStatistics();
        $this->loadRecentAssignments();
    }

    public function loadStatistics()
    {
        if ($this->teacher) {
            $this->totalStudents = $this->teacher->assignedStudents()->count();
            $this->totalAssignments = $this->teacher->assignments()->count();
            $this->totalSubjects = $this->teacher->academicSubjects()->count();
        }
    }

    public function loadRecentAssignments()
    {
        if ($this->teacher) {
            $this->recentAssignments = $this->teacher->assignments()
                ->with('subject')
                ->latest()
                ->take(5)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.teachers.public-profile');
    }
}
