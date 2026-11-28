<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Services\ReceiptService;
use Illuminate\Database\Eloquent\Builder;
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
        $this->validateDateFilters($request);
        $transactions = $this->filteredTransactions($request)->latest()->paginate(20);

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
        $this->validateDateFilters($request);
        $query = $this->filteredTransactions($request);

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Reference',
                'Student',
                'Academic Group',
                'Academic Level',
                'Payer',
                'Amount',
                'Currency',
                'Status',
                'Payment Method',
                'Payment Type',
                'Created At',
                'Paid At',
            ], ',', '"', '');

            $query->chunkById(500, function ($payments) use ($output) {
                foreach ($payments as $payment) {
                    fputcsv($output, [
                        $this->safeCsvValue($payment->reference),
                        $this->safeCsvValue($payment->student->user->name ?? 'N/A'),
                        $this->safeCsvValue($payment->academicGroup->name ?? 'N/A'),
                        $this->safeCsvValue($payment->academicLevel->name ?? 'N/A'),
                        $this->safeCsvValue($payment->getPayerDisplayName()),
                        $payment->amount,
                        $this->safeCsvValue($payment->currency),
                        $this->safeCsvValue($payment->status),
                        $this->safeCsvValue($payment->payment_method ?? ''),
                        $this->safeCsvValue($payment->payment_type),
                        $payment->created_at?->toDateTimeString(),
                        $payment->paid_at?->toDateTimeString(),
                    ], ',', '"', '');
                }
            });

            fclose($output);
        }, 'transactions_'.now()->format('Y-m-d_His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredTransactions(Request $request): Builder
    {
        $query = SchoolPayment::query()
            ->where('school_id', getSchoolId())
            ->with(['student.user', 'payer', 'academicGroup', 'academicLevel']);

        foreach ([
            'status' => 'status',
            'academic_group_id' => 'academic_group_id',
            'academic_level_id' => 'academic_level_id',
            'school_payment_structure_id' => 'payment_structure_id',
            'student_id' => 'student_id',
        ] as $filter => $column) {
            if ($request->filled($filter)) {
                $query->where($column, $request->input($filter));
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%')
                    ->orWhereHas('student.user', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function validateDateFilters(Request $request): void
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }

    private function safeCsvValue(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) ? "'".$value : $value;
    }
}
