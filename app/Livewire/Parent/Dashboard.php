<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\BookSubscription;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class Dashboard extends AppComponent
{
    public $selectedWardId = null;
    public $searchTerm = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::where('user_id', Auth::id())
            ->with(['students.user', 'students.academicLevel.academicGroup', 'students.studentGroup'])
            ->get()
            ->flatMap(function($parent) {
                return $parent->students;
            })
            ->unique('id'); // Remove duplicates

        if ($this->searchTerm) {
            $students = $students->filter(function($student) {
                return stripos($student->user->name, $this->searchTerm) !== false ||
                    stripos($student->academicLevel->name ?? '', $this->searchTerm) !== false ||
                    stripos($student->academicLevel->academicGroup->name ?? '', $this->searchTerm) !== false;
            });
        }

        return $students->sortBy($this->sortBy === 'name' ? 'user.name' : $this->sortBy,
            SORT_REGULAR, $this->sortDirection === 'desc');
    }


    #[Computed]
    public function selectedWard()
    {
        if (!$this->selectedWardId) return null;

        return Student::with([
            'academicLevel.academicGroup',
            'academicGroup',
            'studentGroup',
            'user',
            'assessments' => function($query) {
                $query->latest()->take(5);
            }
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function recentAssessments()
    {
        if (!$this->selectedWard) return collect();

        return Assessment::where('student_id', $this->selectedWardId)
            ->with(['subject'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function academicSummary()
    {
        if (!$this->selectedWard) return [];

        $assessments = Assessment::where('student_id', $this->selectedWardId)->get();

        return [
            'total_assessments' => $assessments->count(),
            'average_score' => $assessments->avg('score') ?? 0,
            'passed_assessments' => $assessments->where('passed', true)->count(),
            'failed_assessments' => $assessments->where('passed', false)->count(),
            'pending_assessments' => $assessments->whereNull('passed')->count(),
        ];
    }

    #[Computed]
    public function bookSubscriptions()
    {
        if (!$this->selectedWard) return collect();

        return BookSubscription::where('student_id', $this->selectedWardId)
            ->with(['book.bookCategory'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function upcomingEvents()
    {
        // This would be connected to your events system
        return collect([
            [
                'title' => 'Parent-Teacher Meeting',
                'date' => now()->addDays(5),
                'type' => 'meeting'
            ],
            [
                'title' => 'Mid-Term Examinations',
                'date' => now()->addDays(10),
                'type' => 'exam'
            ],
            [
                'title' => 'Sports Day',
                'date' => now()->addDays(15),
                'type' => 'event'
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.parent.dashboard');
    }
}
