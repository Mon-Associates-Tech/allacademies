<?php

namespace App\Livewire\SchoolSettings;

use App\Models\AcademicFeeStructure; // Keeping for backward compatibility if needed, but primary is SchoolPaymentStructure
use App\Models\SchoolPaymentStructure;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AcademicGroup;
use App\Models\School;
use App\Models\Subaccount;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Log;

class FeeStructureSetup extends Component
{
    use WithPagination;

    // Form Fields
    public $name;
    public $academic_year_id = '';
    public $academic_group_id = '';
    public $academic_level_id = '';
    public $academic_period_id = ''; // Replaces current_term_id
    public $payment_type = '';
    public $custom_payment_type = ''; // For custom payment types
    public $use_custom_payment_type = false; // Toggle between predefined and custom
    public $subaccount_id = ''; // Account to receive payment
    public $amount = '';
    public $due_date = '';
    public $payment_period = '';
    public $is_mandatory = true;
    public $allow_partial_payment = false;
    public $minimum_partial_amount = '';
    public $description = '';

    // On-the-fly creation fields
    public $new_year_start_date;
    public $new_year_end_date;
    public $new_period_name;
    public $new_period_type = 'term';
    public $new_period_sequence = 1;
    public $new_period_start_date;
    public $new_period_end_date;

    // Collections
    public $academicYears = [];
    public $academicGroups = [];
    public $academicLevels = [];
    public $academicPeriods = [];
    public $paymentTypes = [];
    public $paymentPeriods = [];
    public $schoolSubaccounts = []; // Available subaccounts for the school

    public $showFormModal = false;
    public $formMode = 'create';
    public $editingFeeId = null;
    public $viewingFee = null;

    protected $listeners = ['academicYearCreated' => 'refreshAcademicYears'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
            'academic_level_id' => 'nullable|exists:academic_levels,id',
            'payment_type' => $this->use_custom_payment_type ? 'nullable' : 'required|string',
            'custom_payment_type' => $this->use_custom_payment_type ? 'required|string|max:255' : 'nullable',
            'subaccount_id' => 'nullable|exists:subaccounts,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'payment_period' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'allow_partial_payment' => 'boolean',
            'minimum_partial_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ];

        if (!$this->academic_year_id && $this->new_year_start_date) {
            $rules['new_year_start_date'] = 'required|date';
            $rules['new_year_end_date'] = 'required|date|after:new_year_start_date';
        }

        if (!$this->academic_period_id && $this->new_period_start_date) {
            $rules['new_period_name'] = 'required|string|max:255';
            $rules['new_period_type'] = 'required|string';
            $rules['new_period_sequence'] = 'required|integer|min:1';
            $rules['new_period_start_date'] = 'required|date';
            $rules['new_period_end_date'] = 'required|date|after:new_period_start_date';
        }

        return $rules;
    }

    public function mount()
    {
        $this->loadAcademicYears();
        $this->loadAcademicGroups();
        $this->loadSchoolSubaccounts();
        $this->paymentTypes = SchoolPaymentStructure::paymentTypes();
        $this->paymentPeriods = SchoolPaymentStructure::paymentPeriods();
    }

    /**
     * Load all active subaccounts for the school
     */
    public function loadSchoolSubaccounts()
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            $this->schoolSubaccounts = collect();
            return;
        }


        $this->schoolSubaccounts = Subaccount::whereIn('subaccountable_type', [School::class, 'school'])
            ->where('subaccountable_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'label' => ($account->is_primary ? '[Primary] ' : '') . ($account->name ?? $account->business_name),
                    'name' => $account->name,
                    'business_name' => $account->business_name,
                    'bank' => $account->settlement_bank,
                    'account_number' => $account->account_number,
                ];
            })
            ->toArray();
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

    protected function getSchoolId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if ($user->canAccessCrossSchool()) {
            $sessionSchoolId = session('current_school_id');
            if ($sessionSchoolId) return $sessionSchoolId;
            if (app()->bound('current_school_id')) return app('current_school_id');
            if (app()->bound('current_school')) {
                $school = app('current_school');
                return $school ? $school->id : null;
            }
            return null;
        }

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
        $this->academic_period_id = '';
        $this->loadAcademicPeriods();
    }

    public function updatedAcademicGroupId($value)
    {
        $this->academic_level_id = '';
        $this->loadAcademicLevels();
    }

    public function loadAcademicLevels()
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId || !$this->academic_group_id) {
            $this->academicLevels = [];
            return;
        }

        $this->academicLevels = AcademicLevel::where('school_id', $schoolId)
            ->where('academic_group_id', $this->academic_group_id)
            ->orderBy('name')
            ->get();
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
                ->orderBy('sequence')
                ->get();
        } else {
            $this->academicPeriods = [];
        }
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->loadAcademicYears();
        $this->loadAcademicGroups();
        $this->loadAcademicLevels();
        $this->loadSchoolSubaccounts();
        $this->showFormModal = true;
        $this->formMode = 'create';
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'academic_year_id',
            'academic_group_id',
            'academic_level_id',
            'academic_period_id',
            'payment_type',
            'custom_payment_type',
            'use_custom_payment_type',
            'subaccount_id',
            'amount',
            'due_date',
            'payment_period',
            'is_mandatory',
            'allow_partial_payment',
            'minimum_partial_amount',
            'description',
            'editingFeeId',
            'new_year_start_date', 'new_year_end_date',
            'new_period_name', 'new_period_type', 'new_period_sequence', 'new_period_start_date', 'new_period_end_date'
        ]);
        $this->is_mandatory = true;
        $this->allow_partial_payment = false;
        $this->use_custom_payment_type = false;
        $this->academicLevels = [];
        $this->academicPeriods = [];
        $this->resetErrorBag();
    }

    public function view($id)
    {
        $schoolId = $this->getSchoolId();
        $this->viewingFee = SchoolPaymentStructure::where('school_id', $schoolId)
            ->with([
                'academicGroup',
                'academicLevel',
                'academicPeriod',
                'academicYear'
            ])
            ->findOrFail($id);

        // Only serialize what is needed for view if using x-data, but since we use blade conditional, direct object is fine
        $this->js("window.Modal.open('fee-structure-details')");
    }


    public function closeViewModal()
    {
        $this->viewingFee = null;
    }

    public function edit($id)
    {
        $schoolId = $this->getSchoolId();

        $fee = SchoolPaymentStructure::where('school_id', $schoolId)
            ->findOrFail($id);

        $this->editingFeeId = $fee->id;
        $this->name = $fee->name;
        $this->academic_year_id = $fee->academic_year_id;
        $this->academic_group_id = $fee->academic_group_id;
        $this->academic_level_id = $fee->academic_level_id;
        $this->academic_period_id = $fee->academic_period_id;
        $this->subaccount_id = $fee->subaccount_id;

        // Load academic levels for the selected group
        $this->loadAcademicLevels();

        // Determine if payment type is custom
        $predefinedTypes = SchoolPaymentStructure::paymentTypes();
        if (in_array($fee->payment_type, array_keys($predefinedTypes))) {
            $this->use_custom_payment_type = false;
            $this->payment_type = $fee->payment_type;
            $this->custom_payment_type = '';
        } else {
            $this->use_custom_payment_type = true;
            $this->custom_payment_type = $fee->payment_type;
            $this->payment_type = '';
        }

        $this->amount = $fee->amount;
        $this->due_date = $fee->due_date?->format('Y-m-d');
        $this->payment_period = $fee->payment_period;
        $this->is_mandatory = $fee->is_mandatory;
        $this->allow_partial_payment = $fee->allow_partial_payment;
        $this->minimum_partial_amount = $fee->minimum_partial_amount;
        $this->description = $fee->description;

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
            // Handle on-the-fly creations
            if (!$this->academic_year_id && $this->new_year_start_date) {
                $year = new AcademicYear([
                    'school_id' => $schoolId,
                    'start_date' => $this->new_year_start_date,
                    'end_date' => $this->new_year_end_date,
                    'status' => 'active',
                    'is_current' => true,
                ]);
                $year->name = $year->generateName();
                $year->save();
                $this->academic_year_id = $year->id;
            }

            if (!$this->academic_period_id && $this->new_period_start_date) {
                $period = new AcademicPeriod([
                    'school_id' => $schoolId,
                    'academic_year_id' => $this->academic_year_id ?: null,
                    'name' => $this->new_period_name,
                    'type' => $this->new_period_type,
                    'sequence' => $this->new_period_sequence,
                    'start_date' => $this->new_period_start_date,
                    'end_date' => $this->new_period_end_date,
                    'status' => 'active',
                    'is_current' => true,
                ]);
                if ($period->academic_year_id) {
                    $year = AcademicYear::find($period->academic_year_id);
                    if ($year) $period->academic_year = $year->getDisplayName();
                }
                $period->save();
                $this->academic_period_id = $period->id;
            }

            // Determine payment type (custom or predefined)
            $finalPaymentType = $this->use_custom_payment_type ? $this->custom_payment_type : $this->payment_type;

            $data = [
                'school_id' => $schoolId,
                'name' => $this->name,
                'academic_year_id' => $this->academic_year_id ?: null,
                'academic_period_id' => $this->academic_period_id ?: null,
                'academic_group_id' => $this->academic_group_id ?: null,
                'academic_level_id' => $this->academic_level_id ?: null,
                'payment_type' => $finalPaymentType,
                'subaccount_id' => $this->subaccount_id ?: null,
                'amount' => $this->amount,
                'due_date' => $this->due_date ?: null,
                'payment_period' => $this->payment_period ?: null,
                'is_mandatory' => $this->is_mandatory,
                'allow_partial_payment' => $this->allow_partial_payment,
                'minimum_partial_amount' => $this->minimum_partial_amount ?: null,
                'description' => $this->description,
                'currency' => 'GHS',
            ];

            if ($this->formMode === 'edit' && $this->editingFeeId) {
                $fee = SchoolPaymentStructure::where('school_id', $schoolId)->findOrFail($this->editingFeeId);
                $data['updated_by'] = Auth::id();
                $fee->update($data);
                session()->flash('success', 'Payment structure updated successfully!');
            } else {
                $data['created_by'] = Auth::id();
                SchoolPaymentStructure::create($data);
                session()->flash('success', 'Payment structure created successfully!');
            }

            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Failed to save payment structure: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Payment structure save error: ' . $e->getMessage());
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
            $fee = SchoolPaymentStructure::where('school_id', $schoolId)->findOrFail($id);
            $fee->delete();
            session()->flash('success', 'Payment structure deleted successfully!');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete payment structure. Please try again.');
        }
    }

    public function render()
    {
        $schoolId = $this->getSchoolId();
        $schoolFees = collect();
        $otherFees = collect();

        if ($schoolId) {
            $allFees = SchoolPaymentStructure::where('school_id', $schoolId)
                ->with(['academicGroup', 'academicLevel', 'academicPeriod', 'academicYear'])
                ->latest()
                ->get();

            // Categorize fees: Tuition and Registration as School Fees, others as Other Fees
            $schoolFees = $allFees->filter(function ($fee) {
                return in_array($fee->payment_type, ['tuition', 'admission', 'registration']);
            });

            $otherFees = $allFees->reject(function ($fee) {
                return in_array($fee->payment_type, ['tuition', 'admission', 'registration']);
            });
        }

        return view('livewire.school-settings.fee-structure-setup', [
            'schoolFees' => $schoolFees,
            'otherFees' => $otherFees
        ]);
    }
}
