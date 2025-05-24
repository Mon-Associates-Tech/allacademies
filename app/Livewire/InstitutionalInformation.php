<?php

namespace App\Livewire;

use App\Models\Team;
use Livewire\Component;

class InstitutionalInformation extends Component
{
    public $type;
    public $institution;
    public $college;
    public $school;
    public $faculty;
    public $department;

    public function mount(Team $team)
    {
        $this->type = old('type') ?? data_get($team->meta, 'future.type') ?? data_get($team->meta, 'present.type', 'institution');
        $this->institution = old('institution') ?? data_get($team->meta, 'future.institution') ?? data_get($team->meta, 'present.institution', '');
        $this->college = old('college') ?? data_get($team->meta, 'future.college') ?? data_get($team->meta, 'present.college', '');
        $this->school = old('school') ?? data_get($team->meta, 'future.school') ?? data_get($team->meta, 'present.school', '');
        $this->faculty = old('faculty') ?? data_get($team->meta, 'future.faculty') ?? data_get($team->meta, 'present.faculty', '');
        $this->department = old('department') ?? data_get($team->meta, 'future.department') ?? data_get($team->meta, 'present.department', '');
    }

    public function render()
    {
        return view('livewire.institutional-information');
    }
}
