<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Models\StudentPaymentRecord;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentSetup extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $paymentType = '';
    public $customPaymentType = '';
    public $amount = '';
    public $dueDate = '';
    public $academicYearId = '';
    public $academicPeriodId = '';
    public $academicGroupId = '';
    public $academicLevelId = '';
    public $description = '';
    public $isMandatory = true;
    public $allowPartialPayment = false;
    public $minimumPartialAmount = '';
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'paymentType' => 'required|string',
        'amount' => 'required|numeric|min:0.01',
        'dueDate' => 'nullable|date',
        'academicYearId' => 'nullable|exists:academic_years,id',
        'academicPeriodId' => 'nullable|exists:academic_periods,id',
        'academicGroupId' => 'nullable|exists:academic_groups,id',
        'academicLevelId' => 'nullable|exists:academic_levels,id',
        'description' => 'nullable|string|max:1000',
        'isMandatory' => 'boolean',
        'allowPartialPayment' => 'boolean',
        'minimumPartialAmount' => 'nullable|numeric|min:0',
        'isActive' => 'boolean',
    ];

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->paymentType = '';
        $this->customPaymentType = '';
        $this->amount = '';
        $this->dueDate = '';
        $this->academicYearId = '';
        $this->academicPeriodId = '';
        $this->academicGroupId = '';
        $this->academicLevelId = '';
        $this->description = '';
        $this->isMandatory = true;
        $this->allowPartialPayment = false;
        $this->minimumPartialAmount = '';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $schoolId = getSchoolId();
        
        $finalPaymentType = $this->paymentType === 'custom' ? $this->customPaymentType : $this->paymentType;

        DB::transaction(function () use ($schoolId, $finalPaymentType) {
            $data = [
                'school_id' => $schoolId,
                'name' => $this->name,
                'payment_type' => $finalPaymentType,
                'amount' => $this->amount,
                'currency' => 'GHS',
                'due_date' => $this->dueDate ?: null,
                'academic_year_id' => $this->academicYearId ?: null,
                'academic_period_id' => $this->academicPeriodId ?: null,
                'academic_group_id' => $this->academicGroupId ?: null,
                'academic_level_id' => $this->academicLevelId ?: null,
                'description' => $this->description,
                'is_mandatory' => $this->isMandatory,
                'allow_partial_payment' => $this->allowPartialPayment,
                'minimum_partial_amount' => $this->minimumPartialAmount ?: null,
                'is_active' => $this->isActive,
                'created_by' => auth()->id(),
            ];

            if ($this->editingId) {
                $structure = SchoolPaymentStructure::findOrFail($this->editingId);
                $structure->update($data);
                $message = 'Payment structure updated successfully!';
            } else {
                $structure = SchoolPaymentStructure::create($data);
                
                // Create student payment records for applicable students
                $this->createStudentPaymentRecords($structure);
                
                $message = 'Payment structure created and assigned to students!';
            }

            session()->flash('message', $message);
        });

        $this->showModal = false;
        $this->resetForm();
    }

    protected function createStudentPaymentRecords(SchoolPaymentStructure $structure)
    {
        $students = $structure->getApplicableStudents();

        foreach ($students as $student) {
            StudentPaymentRecord::create([
                'school_id' => $structure->school_id,
                'student_id' => $student->id,
                'payment_structure_id' => $structure->id,
                'academic_year_id' => $structure->academic_year_id,
                'academic_period_id' => $structure->academic_period_id,
                'payment_type' => $structure->payment_type,
                'description' => $structure->description,
                'total_amount' => $structure->amount,
                'amount_paid' => 0,
                'amount_remaining' => $structure->amount,
                'currency' => $structure->currency,
                'due_date' => $structure->due_date,
                'status' => 'unpaid',
                'is_custom' => false,
            ]);
        }
    }

    public function edit($id)
    {
        $structure = SchoolPaymentStructure::findOrFail($id);
        
        $this->editingId = $structure->id;
        $this->name = $structure->name;
        $this->paymentType = $structure->payment_type;
        $this->amount = $structure->amount;
        $this->dueDate = $structure->due_date?->format('Y-m-d');
        $this->academicYearId = $structure->academic_year_id;
        $this->academicPeriodId = $structure->academic_period_id;
        $this->academicGroupId = $structure->academic_group_id;
        $this->academicLevelId = $structure->academic_level_id;
        $this->description = $structure->description;
        $this->isMandatory = $structure->is_mandatory;
        $this->allowPartialPayment = $structure->allow_partial_payment;
        $this->minimumPartialAmount = $structure->minimum_partial_amount;
        $this->isActive = $structure->is_active;
        
        $this->showModal = true;
    }

    public function delete($id)
    {
        SchoolPaymentStructure::findOrFail($id)->delete();
        session()->flash('message', 'Payment structure deleted successfully!');
    }

    public function render()
    {
        $schoolId = getSchoolId();

        $structures = SchoolPaymentStructure::where('school_id', $schoolId)
            ->with(['academicYear', 'academicPeriod', 'academicGroup', 'academicLevel'])
            ->latest()
            ->paginate(15);

        $paymentTypes = \App\Models\SchoolPayment::paymentTypes();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $academicPeriods = AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $academicGroups = AcademicGroup::forSchool($schoolId)->get();
        $academicLevels = AcademicLevel::forSchool($schoolId)->get();

        return view('livewire.administrators.payment-setup', [
            'structures' => $structures,
            'paymentTypes' => $paymentTypes,
            'academicYears' => $academicYears,
            'academicPeriods' => $academicPeriods,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
        ]);
    }
}
