<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\SchoolFee;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentFeesManager extends AppComponent
{
    use WithPagination;

    public $selectedStudentId = null;

    public $showPaymentModal = false;

    public $paymentType = 'school_fee'; // school_fee or school_payment

    public $amount = '';

    public $selectedTerm = null;

    protected $rules = [
        'selectedStudentId' => 'required|exists:students,id',
        'amount' => 'required|numeric|min:1',
        'paymentType' => 'required|in:school_fee,school_payment',
    ];

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
            $this->selectedStudentId = $wards->first()->id;
        }
    }

    public function selectStudent($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->reset(['amount', 'paymentType']);
    }

    public function openPaymentModal($studentId = null, $type = 'school_fee')
    {
        if ($studentId) {
            $this->selectedStudentId = $studentId;
        }

        $this->paymentType = $type;

        // Auto-fill remaining amount for school fees
        if ($type === 'school_fee' && $this->selectedStudent) {
            $feeData = $this->getStudentFeeData($this->selectedStudent);
            $this->amount = $feeData['remainingAmount'];
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['amount']);
    }

    #[Computed]
    public function wards()
    {
        $parent = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->first();

        if (! $parent) {
            return collect();
        }

        return $parent->students()
            ->withoutGlobalScopes()
            ->with(['user', 'academicLevel', 'academicGroup'])
            ->get();
    }

    #[Computed]
    public function selectedStudent()
    {
        if (! $this->selectedStudentId) {
            return null;
        }

        return Student::withoutGlobalScopes()
            ->with(['user', 'academicLevel', 'academicGroup'])
            ->find($this->selectedStudentId);
    }

    #[Computed]
    public function currentTerm()
    {
        $schoolId = getSchoolId();

        return AcademicPeriod::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->orWhere('status', 'active')
            ->first();
    }

    #[Computed]
    public function studentsWithFees()
    {
        $wards = $this->wards;
        $currentTerm = $this->currentTerm;

        return $wards->map(function ($student) use ($currentTerm) {
            return array_merge(
                ['student' => $student],
                $this->getStudentFeeData($student, $currentTerm)
            );
        });
    }

    private function getStudentFeeData($student, $currentTerm = null)
    {
        if (! $currentTerm) {
            $currentTerm = $this->currentTerm;
        }

        $schoolId = getSchoolId();

        $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        $totalPaidSchoolFees = SchoolFee::where('student_id', $student->id)
            ->where('term_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $totalPaidSchoolPayments = SchoolPayment::where('student_id', $student->id)
            ->where('academic_period_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
        $totalPaid = $totalPaidSchoolFees + $totalPaidSchoolPayments;
        $remainingAmount = max($termTotalAmount - $totalPaid, 0);

        return [
            'feeStructure' => $feeStructure,
            'totalPaid' => $totalPaid,
            'termTotalAmount' => $termTotalAmount,
            'remainingAmount' => $remainingAmount,
            'schoolFeesCount' => SchoolFee::where('student_id', $student->id)->count(),
            'schoolPaymentsCount' => SchoolPayment::where('student_id', $student->id)->count(),
        ];
    }

    #[Computed]
    public function paymentHistory()
    {
        if (! $this->selectedStudentId) {
            return collect();
        }

        $schoolFees = SchoolFee::where('student_id', $this->selectedStudentId)
            ->with(['payer', 'academicPeriod'])
            ->get()
            ->map(function ($fee) {
                $fee->payment_category = 'School Fee';

                return $fee;
            });

        $schoolPayments = SchoolPayment::where('student_id', $this->selectedStudentId)
            ->with(['payer', 'academicPeriod'])
            ->get()
            ->map(function ($payment) {
                $payment->payment_category = 'School Payment';

                return $payment;
            });

        return $schoolFees->concat($schoolPayments)
            ->sortByDesc('created_at')
            ->take(20);
    }

    public function render()
    {
        return view('livewire.parent.parent-fees-manager');
    }
}
