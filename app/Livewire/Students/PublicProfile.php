<?php

namespace App\Livewire\Students;

use App\Models\User;
use Livewire\Component;

class PublicProfile extends Component
{
    public User $user;
    public $student;
    public $totalAssessments = 0;
    public $averageScore = 0;
    public $recentActivity;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->student = $user->student;

        if (!$this->student) {
            abort(404, 'Student profile not found.');
        }

        $this->loadStatistics();
        $this->loadRecentActivity();
    }

    public function loadStatistics()
    {
        if ($this->student) {
            $this->totalAssessments = $this->student->assessments()->count();
            $this->averageScore = round($this->student->assessments()
                ->where('status', 'completed')
                ->avg('score') ?? 0, 1);
        }
    }

    public function loadRecentActivity()
    {
        if ($this->student) {
            $this->recentActivity = $this->student->assessments()
                ->with(['subject', 'topic'])
                ->latest()
                ->take(5)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.students.public-profile')
            ->layout('layouts.app', [
                'title' => $this->user->name . ' - Student Profile'
            ]);
    }
}
