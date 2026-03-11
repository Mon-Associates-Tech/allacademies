<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private FinancialReportService $reportService
    ) {}

    public function index()
    {
        $schoolId = getSchoolId();
        
        // Get filter options
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $academicPeriods = AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $academicLevels = AcademicLevel::whereHas('schools', fn($q) => $q->where('school_id', $schoolId))->get();
        $academicGroups = AcademicGroup::whereHas('schools', fn($q) => $q->where('school_id', $schoolId))->get();
        $paymentTypes = SchoolPayment::paymentTypes();
        
        return view('accountant.reports.index', compact(
            'academicYears',
            'academicPeriods',
            'academicLevels',
            'academicGroups',
            'paymentTypes'
        ));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string|in:payment_summary,student_payments,outstanding_payments,revenue,financial_aid,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv,view',
            'student_id' => 'nullable|exists:students,id',
            'payment_type' => 'nullable|string',
            'status' => 'nullable|string',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_level_id' => 'nullable|exists:academic_levels,id',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
            'payment_method' => 'nullable|string',
        ]);

        $filters = array_merge($validated, ['school_id' => getSchoolId()]);
        $reportType = $validated['report_type'];
        $format = $validated['format'];

        // Generate report data
        $data = match ($reportType) {
            'payment_summary' => $this->reportService->getPaymentSummary($filters),
            'student_payments' => $this->reportService->getStudentPayments($filters),
            'outstanding_payments' => $this->reportService->getOutstandingPayments($filters),
            'revenue' => $this->reportService->getRevenueReport($filters),
            'financial_aid' => $this->reportService->getFinancialAidReport($filters),
            'custom' => $this->reportService->getCustomReport($filters),
        };

        $data['filters'] = $filters;
        $data['report_type'] = $reportType;
        $data['generated_at'] = now();
        $data['school'] = getCurrentSchoolContext();

        // Return based on format
        return match ($format) {
            'pdf' => $this->generatePdf($reportType, $data),
            'excel' => $this->generateExcel($reportType, $data),
            'csv' => $this->generateCsv($reportType, $data),
            'view' => view('accountant.reports.view', $data),
        };
    }

    private function generatePdf(string $reportType, array $data)
    {
        $pdf = Pdf::loadView('accountant.reports.pdf.' . str_replace('_', '-', $reportType), $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);

        $filename = $reportType . '_report_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    private function generateExcel(string $reportType, array $data)
    {
        $filename = $reportType . '_report_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new \App\Exports\FinancialReportExport($reportType, $data),
            $filename
        );
    }

    private function generateCsv(string $reportType, array $data)
    {
        $filename = $reportType . '_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return Excel::download(
            new \App\Exports\FinancialReportExport($reportType, $data),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function export(Request $request)
    {
        return $this->generate($request);
    }

    public function receipt(SchoolPayment $payment)
    {
        $receiptService = app(\App\Services\ReceiptService::class);
        $pdf = $receiptService->generateReceipt($payment);
        
        return $pdf->download('receipt_' . $payment->reference . '.pdf');
    }

    public function bulkReceipts(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:school_payments,id',
        ]);

        $receiptService = app(\App\Services\ReceiptService::class);
        $pdf = $receiptService->generateBulkReceipts($request->payment_ids);
        
        return $pdf->download('bulk_receipts_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
