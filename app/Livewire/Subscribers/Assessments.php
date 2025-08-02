<?php

namespace App\Livewire\Subscribers;

use App\Models\Assessment;
use App\Models\AcademicSubject as Subject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Assessments extends Component
{
    public $currentView = 'dashboard'; // 'dashboard', 'take-assessment', 'results'
    public $selectedSubject = null;
    public $assessmentHistory = null;
    public $subjects = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        $this->assessmentHistory = collect();

        if ($user->student) {
            $this->assessmentHistory = Assessment::where('student_id', $user->student->id)
                ->with(['subject', 'topic'])
                ->latest()
                ->take(10)
                ->get()->collect();
        }

        $this->subjects = Subject::get();
    }

    public function startSelfAssessment()
    {
        $this->currentView = 'take-assessment';
    }

    public function backToDashboard()
    {
        $this->currentView = 'dashboard';
    }

    public function render()
    {
        return view('livewire.subscribers.assessments');
    }
}
