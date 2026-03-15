<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function index(Request $request)
    {
        $schoolId = getSchoolId();

        $query = SchoolPayment::query()
            ->where('school_id', $schoolId)
            ->with(['student.user', 'payer', 'academicGroup', 'academicLevel']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_group_id')) {
            $query->where('academic_group_id', $request->academic_group_id);
        }

        if ($request->filled('academic_level_id')) {
            $query->where('academic_level_id', $request->academic_level_id);
        }

        if ($request->filled('school_payment_structure_id')) {
            $query->where('payment_structure_id', $request->school_payment_structure_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%'.$request->search.'%')
                    ->orWhereHas('student.user', function ($sq) use ($request) {
                        $sq->where('name', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $transactions = $query->latest()->paginate(20);

        $academicGroups = AcademicGroup::forCurrentSchool()->get();
        $academicLevels = AcademicLevel::forCurrentSchool()->get();
        $paymentStructures = SchoolPaymentStructure::where('school_id', $schoolId)->get();
        $students = Student::where('school_id', $schoolId)->with('user')->get();

        return view('accountant.transactions.index', compact(
            'transactions',
            'academicGroups',
            'academicLevels',
            'paymentStructures',
            'students'
        ));
    }

    public function show(SchoolPayment $payment)
    {
        $payment->load(['student', 'payer', 'academicGroup', 'academicLevel', 'academicYear', 'academicPeriod']);

        return view('accountant.transactions.show', compact('payment'));
    }

    public function receipt(SchoolPayment $payment)
    {
        $payment->load(['student.user', 'payer', 'school', 'academicGroup', 'academicLevel', 'academicYear', 'academicPeriod']);

        return view('accountant.receipts.show', compact('payment'));
    }

    public function receiptPdf(SchoolPayment $payment)
    {
        $pdf = $this->receiptService->generateReceipt($payment);

        return $pdf->download('receipt-'.$payment->reference.'.pdf');
    }

    public function export(Request $request)
    {
        $schoolId = getSchoolId();

        $query = SchoolPayment::query()
            ->where('school_id', $schoolId)
            ->with(['student', 'payer', 'academicGroup', 'academicLevel']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->get();

        // For now, return success message
        // In production, you would generate actual export file
        return back()->with('success', 'Export functionality coming soon');
    }
}
