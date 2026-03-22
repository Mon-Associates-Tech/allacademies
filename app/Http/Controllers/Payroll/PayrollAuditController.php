<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;

class PayrollAuditController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:viewPayroll,App\Models\User');
    }

    public function index()
    {
        return view('payroll.audit.index');
    }
}
