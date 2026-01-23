<?php

namespace App\Livewire\School;

use App\Models\School;
use Livewire\Component;

class SchoolDetails extends Component
{
    public School $school;

    public $stats = [];

    public $academicGroups = [];

    public $academicLevels = [];

    public $recentPeriods = [];

    public $subaccounts = [];

    public function mount($schoolId)
    {
        if (! auth()->user()->canAccessCrossSchool()) {
            abort(403);
        }

        $this->school = School::with([
            'academicGroups',
            'academicLevels',
            'academicPeriods' => fn ($q) => $q->latest()->limit(5),
            'currentAcademicPeriod',
        ])->findOrFail($schoolId);

        $this->loadStats();
        $this->loadAcademicData();
        $this->loadSubaccounts();
    }

    public function loadStats(): void
    {
        $this->stats = $this->school->getStats();
    }

    public function loadAcademicData(): void
    {
        $this->academicGroups = $this->school->academicGroups;
        $this->academicLevels = $this->school->academicLevels;
        $this->recentPeriods = $this->school->academicPeriods()->latest()->limit(5)->get();
    }

    public function loadSubaccounts(): void
    {
        $this->subaccounts = $this->school->subaccounts ?? [];
    }

    public function render()
    {
        return view('livewire.school.school-details');
    }
}
