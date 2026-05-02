<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\StudentPaymentRecord;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StudentPaymentDetails extends Component
{
    use WithPagination;

    public Student $student;
    
    // One-off payment form
    public $showOneOffPaymentModal = false;
    public $oneOffAmount = '';
    public $oneOffPaymentType = '';
    public $oneOffDescription = '';
    public $oneOffDueDate = '';
    public $oneOffAcademicYear = '';
    public $oneOffAcademicPeriod = '';

    protected $rules = [
        'oneOffAmount' => 'required|numeric|min:0.01',
        'oneOffPaymentType' => 'required|string',
        'oneOffDescription' => 'nullable|string|max:500',
        'oneOffDueDate' => 'nullable|date',
        'oneOffAcademicYear' => 'nullable|exists:academic_years,id',
        'oneOffAcademicPeriod' => 'nullable|exists:academic_periods,id',
    ];

    public function mount(Student $student)
    {
        $this->student = $student->load([
            'user',
            'academicGroup',
            'academicLevel',
        ]);
    }

    public function showOneOffPaymentForm()
    {
        $this->showOneOffPaymentModal = true;
        $this->resetOneOffForm();
    }

    public function resetOneOffForm()
    {
        $this->oneOffAmount = '';
        $this->oneOffPaymentType = '';
        $this->oneOffDescription = '';
        $this->oneOffDueDate = '';
        $this->oneOffAcademicYear = '';
        $this->oneOffAcademicPeriod = '';
        $this->resetValidation();
    }

    public function createOneOffPayment()
    {
        $this->validate();

        $schoolId = getSchoolId();

        DB::transaction(function () use ($schoolId) {
            StudentPaymentRecord::create([
                'school_id' => $schoolId,
                'student_id' => $this->student->id,
                'academic_year_id' => $this->oneOffAcademicYear ?: null,
                'academic_period_id' => $this->oneOffAcademicPeriod ?: null,
                'payment_type' => $this->oneOffPaymentType,
                'description' => $this->oneOffDescription,
                'total_amount' => $this->oneOffAmount,
                'amount_paid' => 0,
                'amount_remaining' => $this->oneOffAmount,
                'currency' => 'GHS',
                'due_date' => $this->oneOffDueDate ?: null,
                'status' => 'unpaid',
                'is_custom' => true,
            ]);
        });

        $this->showOneOffPaymentModal = false;
        $this->resetOneOffForm();
        session()->flash('message', 'One-off payment created successfully!');
        $this->dispatch('$refresh');
    }

    public function waivePayment($recordId, $reason = null)
    {
        $record = StudentPaymentRecord::findOrFail($recordId);
        $record->waive(auth()->user(), $reason);
        
        session()->flash('message', 'Payment waived successfully!');
        $this->dispatch('$refresh');
    }

    public function applyDiscount($recordId, $discountAmount)
    {
        $record = StudentPaymentRecord::findOrFail($recordId);
        
        $record->update([
            'discount_amount' => $discountAmount,
            'total_amount' => $record->total_amount - $discountAmount,
        ]);
        
        $record->updatePaymentStatus();
        
        session()->flash('message', 'Discount applied successfully!');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $schoolId = getSchoolId();

        // Get all payment records for this student
        $paymentRecords = StudentPaymentRecord::where('student_id', $this->student->id)
            ->where('school_id', $schoolId)
            ->with(['paymentStructure', 'academicYear', 'academicPeriod', 'transactions'])
            ->orderBy('due_date', 'desc')
            ->paginate(10, ['*'], 'recordsPage');

        // Get all transactions for this student
        $transactions = SchoolPayment::where('student_id', $this->student->id)
            ->where('school_id', $schoolId)
            ->with(['academicYear', 'academicPeriod', 'payer'])
            ->latest()
            ->paginate(10, ['*'], 'transactionsPage');

        // Calculate summary
        $summary = [
            'total_expected' => StudentPaymentRecord::where('student_id', $this->student->id)
                ->where('school_id', $schoolId)
                ->sum('total_amount'),
            'total_paid' => StudentPaymentRecord::where('student_id', $this->student->id)
                ->where('school_id', $schoolId)
                ->sum('amount_paid'),
            'total_outstanding' => StudentPaymentRecord::where('student_id', $this->student->id)
                ->where('school_id', $schoolId)
                ->sum('amount_remaining'),
            'overdue_count' => StudentPaymentRecord::where('student_id', $this->student->id)
                ->where('school_id', $schoolId)
                ->overdue()
                ->count(),
        ];

        // Get filter options for one-off payment
        $paymentTypes = SchoolPayment::paymentTypes();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $academicPeriods = AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('livewire.administrators.student-payment-details', [
            'paymentRecords' => $paymentRecords,
            'transactions' => $transactions,
            'summary' => $summary,
            'paymentTypes' => $paymentTypes,
            'academicYears' => $academicYears,
            'academicPeriods' => $academicPeriods,
        ]);
    }
}
