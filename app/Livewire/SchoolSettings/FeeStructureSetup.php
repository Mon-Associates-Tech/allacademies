<?php

namespace App\Livewire\SchoolSettings;

use App\Models\AcademicFeeStructure;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FeeStructureSetup extends Component
{
    public $academic_group_id = '';
    public $academic_level_id = '';
    public $current_term_id = '';
    public $amount = '';
    public $due_date = '';
    public $payment_method = 'Momo';

    public $academicGroups = [];
    public $academicLevels = [];
    public $academicPeriods = [];

    protected $rules = [
        'academic_group_id' => 'required|exists:academic_groups,id',
        'academic_level_id' => 'required|exists:academic_levels,id',
        'current_term_id' => 'required|exists:academic_periods,id',
        'amount' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'payment_method' => 'nullable|string|max:50',
    ];

    protected $messages = [
        'academic_group_id.required' => 'Please select an academic group',
        'academic_level_id.required' => 'Please select an academic level',
        'current_term_id.required' => 'Please select a term',
        'amount.required' => 'Amount is required',
        'amount.numeric' => 'Amount must be a valid number',
        'amount.min' => 'Amount must be greater than or equal to 0',
        'due_date.required' => 'Due date is required',
        'due_date.date' => 'Due date must be a valid date',
    ];

    public function mount()
    {
        $this->loadAcademicGroups();
        $this->loadAcademicPeriods();
    }

    public function loadAcademicGroups()
    {
        $school = Auth::user()->school;

        if ($school) {
            $this->academicGroups = $school->academicGroups()
                ->orderBy('name')
                ->get();
        }
    }

    public function loadAcademicPeriods(): void
    {
        $this->academicPeriods = AcademicPeriod::where('school_id', Auth::user()->school_id)
            ->whereIn('status', ['active', 'upcoming'])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function updatedAcademicGroupId($value)
    {
        $this->academic_level_id = '';
        $this->loadAcademicLevels();
    }

    public function loadAcademicLevels()
    {
        if ($this->academic_group_id) {
            $this->academicLevels = AcademicLevel::where('academic_group_id', $this->academic_group_id)
                ->orderBy('name')
                ->get();
        } else {
            $this->academicLevels = [];
        }
    }

    public function save()
    {
        $this->validate();

        try {
            AcademicFeeStructure::create([
                'school_id' => Auth::user()->school_id,
                'academic_group_id' => $this->academic_group_id,
                'academic_level_id' => $this->academic_level_id,
                'current_term_id' => $this->current_term_id,
                'amount' => $this->amount,
                'due_date' => $this->due_date,
                'payment_method' => $this->payment_method,
            ]);

            session()->flash('success', 'Fee structure created successfully!');

            return redirect()->route('school-settings.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create fee structure. Please try again.');
            \Log::error('Fee structure creation error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.school-settings.fee-structure-setup');
    }
}
