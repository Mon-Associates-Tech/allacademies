<?php

namespace App\Livewire\School;

use App\Models\School;
use Livewire\Component;

class SchoolDetails extends Component
{
    public School $school;
    public $stats = [];

    public function mount($schoolId)
    {
        if (!auth()->user()->canAccessCrossSchool()) {
            abort(403);
        }

        $this->school = School::findOrFail($schoolId);
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = $this->school->getStats();
    }

    public function render()
    {
        return view('livewire.school.school-details');
    }
}

