<?php

namespace App\Livewire\SchoolSettings;

use App\Models\AcademicFeeStructure;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Log;

class FeeStructureSetup extends Component
{
    use WithPagination;

    public $academic_year_id = '';
    public $academic_group_id = '';
    public $academic_level_id = '';
    public $current_term_id = '';
    public $amount = '';
    public $due_date = '';
    public $payment_method = 'Momo';

    public $academicYears = [];
    public $academicGroups = [];
    public $academicLevels = [];
    public $academicPeriods = [];

    public $showFormModal = false;
    public $formMode = 'create';
    public $editingFeeId = null;
    public $viewingFee = null;

    protected $listeners = ['academicYearCreated' => 'refreshAcademicYears'];

    protected $rules = [
        'academic_year_id' => 'required|exists:academic_years,id',
        'academic_group_id' => 'required|exists:academic_groups,id',
        'academic_level_id' => 'required|exists:academic_levels,id',
        'current_term_id' => 'required|exists:academic_periods,id',
        'amount' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'payment_method' => 'nullable|string|max:50',
    ];

    protected $messages = [
        'academic_year_id.required' => 'Please select an academic year',
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
        $this->loadAcademicYears();
        $this->loadAcademicGroups();
    }

    public function loadAcademicYears()
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            $this->academicYears = collect();
            return;
        }

        $this->academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get school ID from context (works for owners, admins, and regular users)
     */
    protected function getSchoolId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // For owners/super admins, check session for selected school
        if ($user->canAccessCrossSchool()) {
            $sessionSchoolId = session('current_school_id');

            // If they've explicitly selected a school, use it
            if ($sessionSchoolId) {
                return $sessionSchoolId;
            }

            // Check app binding
            if (app()->bound('current_school_id')) {
                return app('current_school_id');
            }

            // Check if current_school is bound
            if (app()->bound('current_school')) {
                $school = app('current_school');
                return $school ? $school->id : null;
            }

            // No school selected - return null
            return null;
        }

        // For regular users, use their school_id
        return $user->school_id;
    }

    public function loadAcademicGroups()
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            $this->academicGroups = collect();
            return;
        }

        $school = School::find($schoolId);

        if ($school) {
            $this->academicGroups = $school->academicGroups()
                ->orderBy('name')
                ->get();
        }
    }

    public function refreshAcademicYears()
    {
        $this->loadAcademicYears();
    }

    public function updatedAcademicYearId($value)
    {
        $this->current_term_id = '';
        $this->loadAcademicPeriods();
    }

    public function loadAcademicPeriods()
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            $this->academicPeriods = [];
            return;
        }

        if ($this->academic_year_id) {
            $this->academicPeriods = AcademicPeriod::where('school_id', $schoolId)
                ->where('academic_year_id', $this->academic_year_id)
                ->whereIn('status', ['active', 'upcoming'])
                ->orderBy('sequence')
                ->get();
        } else {
            $this->academicPeriods = [];
        }
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

    public function showCreateForm()
    {
        $this->resetForm();
        $this->loadAcademicYears(); // Refresh academic years when opening the form
        $this->loadAcademicGroups(); // Also refresh groups
        $this->showFormModal = true;
        $this->formMode = 'create';
    }

    public function resetForm()
    {
        $this->reset([
            'academic_year_id',
            'academic_group_id',
            'academic_level_id',
            'current_term_id',
            'amount',
            'due_date',
            'payment_method',
            'editingFeeId'
        ]);
        $this->payment_method = 'Momo';
        $this->academicLevels = [];
        $this->academicPeriods = [];
        $this->resetErrorBag();
    }

    public function view($id)
    {
        $schoolId = $this->getSchoolId();
        $this->viewingFee = AcademicFeeStructure::where('school_id', $schoolId)
            ->with([
                'academicGroup',
                'academicLevel',
                'currentTerm',
                'currentTerm.academicYear'
            ])
            ->findOrFail($id);

        // Properly serialize the data for JavaScript
        $viewingFeeData = [
            'id' => $this->viewingFee->id,
            'amount' => $this->viewingFee->amount,
            'formatted_amount' => $this->viewingFee->formatted_amount,
            'due_date' => $this->viewingFee->due_date,
            'formatted_due_date' => $this->viewingFee->formatted_due_date,
            'payment_method' => $this->viewingFee->payment_method,
            'academicGroup' => [
                'name' => $this->viewingFee->academicGroup->name
            ],
            'academicLevel' => [
                'name' => $this->viewingFee->academicLevel->name
            ],
            'currentTerm' => [
                'display_name' => $this->viewingFee->currentTerm->getDisplayName(),
                'academicYear' => $this->viewingFee->currentTerm->academicYear ? [
                    'getDisplayName' => $this->viewingFee->currentTerm->academicYear->getDisplayName()
                ] : null
            ]
        ];

        $this->js("window.Modal.open('fee-structure-details', {viewingFee: " . json_encode($viewingFeeData) . "})");
    }


    public function closeViewModal()
    {
        $this->viewingFee = null;
    }

    public function edit($id)
    {
        $schoolId = $this->getSchoolId();

        $fee = AcademicFeeStructure::where('school_id', $schoolId)
            ->with('currentTerm')
            ->findOrFail($id);

        $this->editingFeeId = $fee->id;
        $this->academic_year_id = $fee->currentTerm->academic_year_id ?? '';
        $this->academic_group_id = $fee->academic_group_id;
        $this->academic_level_id = $fee->academic_level_id;
        $this->current_term_id = $fee->current_term_id;
        $this->amount = $fee->amount;
        $this->due_date = $fee->due_date;
        $this->payment_method = $fee->payment_method;

        $this->loadAcademicPeriods();
        $this->loadAcademicLevels();

        $this->formMode = 'edit';
        $this->showFormModal = true;
    }

    public function save()
    {
        $this->validate();

        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'No school context found. Please select a school.');
            return;
        }

        try {
            if ($this->formMode === 'edit' && $this->editingFeeId) {
                // Update existing fee structure
                $fee = AcademicFeeStructure::where('school_id', $schoolId)
                    ->findOrFail($this->editingFeeId);

                $fee->update([
                    'academic_group_id' => $this->academic_group_id,
                    'academic_level_id' => $this->academic_level_id,
                    'current_term_id' => $this->current_term_id,
                    'amount' => $this->amount,
                    'due_date' => $this->due_date,
                    'payment_method' => $this->payment_method,
                ]);

                session()->flash('success', 'Fee structure updated successfully!');
            } else {
                // Create new fee structure
                AcademicFeeStructure::create([
                    'school_id' => $schoolId,
                    'academic_group_id' => $this->academic_group_id,
                    'academic_level_id' => $this->academic_level_id,
                    'current_term_id' => $this->current_term_id,
                    'amount' => $this->amount,
                    'due_date' => $this->due_date,
                    'payment_method' => $this->payment_method,
                ]);

                session()->flash('success', 'Fee structure created successfully!');
            }

            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Failed to save fee structure. Please try again.');
            Log::error('Fee structure save error: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            $schoolId = $this->getSchoolId();

            $fee = AcademicFeeStructure::where('school_id', $schoolId)
                ->findOrFail($id);

            $fee->delete();

            session()->flash('success', 'Fee structure deleted successfully!');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete fee structure. Please try again.');
            Log::error('Fee structure deletion error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $schoolId = $this->getSchoolId();

        $feeStructures = collect();

        if ($schoolId) {
            $feeStructures = AcademicFeeStructure::where('school_id', $schoolId)
                ->with(['academicGroup', 'academicLevel', 'currentTerm.academicYear'])
                ->latest()
                ->paginate(10);
        }

        return view('livewire.school-settings.fee-structure-setup', [
            'feeStructures' => $feeStructures
        ]);
    }
}
