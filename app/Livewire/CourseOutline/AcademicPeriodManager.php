<?php

namespace App\Livewire\CourseOutline;

use App\Models\AcademicPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicPeriodManager extends Component
{
    use WithPagination;

    public $name;
    public $type;
    public $startDate;
    public $endDate;
    public $description;
    public $academicYear;
    public $isActive = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:semester,term',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after:startDate',
        'description' => 'nullable|string',
        'academicYear' => 'required|string',
        'isActive' => 'boolean',
    ];

    public function createPeriod()
    {
        $this->validate();

        AcademicPeriod::create([
            'name' => $this->name,
            'type' => $this->type,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
            'academic_year' => $this->academicYear,
            'is_active' => $this->isActive,
        ]);

        $this->reset();
        session()->flash('message', 'Academic period created successfully.');
    }

    public function render()
    {
        return view('livewire.teachers.course.academic-period-manager', [
            'periods' => AcademicPeriod::orderBy('start_date', 'desc')->paginate(10)
        ]);
    }
}
