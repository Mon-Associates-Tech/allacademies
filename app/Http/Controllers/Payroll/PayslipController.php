<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollDisbursement;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('can:viewPayroll,App\Models\User');
    }

    public function show(PayrollDisbursement $disbursement)
    {
        $disbursement->load(['payrollEntry', 'bankAccount', 'payrollRun.schedule']);
        
        return view('payroll.payslip', compact('disbursement'));
    }

    public function download(PayrollDisbursement $disbursement)
    {
        $disbursement->load(['payrollEntry', 'bankAccount', 'payrollRun.schedule', 'school']);
        
        $pdf = Pdf::loadView('payroll.payslip', compact('disbursement'));
        
        $filename = 'payslip-' . $disbursement->payrollEntry->full_name . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
