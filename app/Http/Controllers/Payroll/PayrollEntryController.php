<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollEntry;
use App\Models\PayrollRole;
use App\Models\User;
use App\Services\PayrollEntryService;
use App\Services\PaystackTransferService;
use Illuminate\Http\Request;

class PayrollEntryController extends Controller
{
    public function __construct(
        protected PayrollEntryService $entryService,
        protected PaystackTransferService $paystackService
    ) {
       // $this->middleware('can:managePayroll,App\Models\User');
    }

    public function index()
    {
        return view('payroll.entries.index');
    }

    public function create()
    {
        $schoolId = getSchoolId() ?? auth()->user()->school_id;
        
        $payrollRoles = PayrollRole::where('school_id', $schoolId)->get();
        $systemUsers = User::where('school_id', $schoolId)
            ->whereIn('role', ['admin', 'accountant', 'teacher'])
            ->get();
        
        return view('payroll.entries.create', compact('payrollRoles', 'systemUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'payroll_role_id' => 'nullable|exists:payroll_roles,id',
            'system_role' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'gross_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
        ]);
        
        $entry = $this->entryService->createEntry($validated, auth()->user());
        
        return redirect()->route('payroll.entries.index')
            ->with('success', 'Payroll entry created successfully.');
    }

    public function edit(PayrollEntry $entry)
    {
        $schoolId = getSchoolId() ?? auth()->user()->school_id;
        
        $payrollRoles = PayrollRole::where('school_id', $schoolId)->get();
        $systemUsers = User::where('school_id', $schoolId)
            ->whereIn('role', ['admin', 'accountant', 'teacher'])
            ->get();
        
        return view('payroll.entries.edit', compact('entry', 'payrollRoles', 'systemUsers'));
    }

    public function update(Request $request, PayrollEntry $entry)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'payroll_role_id' => 'nullable|exists:payroll_roles,id',
            'system_role' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'gross_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
        ]);
        
        $this->entryService->updateEntry($entry, $validated, auth()->user());
        
        return redirect()->route('payroll.entries.index')
            ->with('success', 'Payroll entry updated successfully.');
    }

    public function destroy(PayrollEntry $entry)
    {
        $entry->delete();
        
        return redirect()->route('payroll.entries.index')
            ->with('success', 'Payroll entry deleted successfully.');
    }

    public function storeBankAccount(Request $request, PayrollEntry $entry)
    {
        $validated = $request->validate([
            'account_number' => 'required|string|size:10',
            'bank_code' => 'required|string',
            'bank_name' => 'required|string',
        ]);
        
        try {
            $this->entryService->attachBankAccount($entry, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Bank account verified and attached successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function verifyBankAccount(Request $request, PayrollEntry $entry)
    {
        $validated = $request->validate([
            'account_number' => 'required|string|size:10',
            'bank_code' => 'required|string',
        ]);
        
        try {
            $accountName = $this->paystackService->resolveAccountName(
                $validated['account_number'],
                $validated['bank_code']
            );
            
            return response()->json([
                'success' => true,
                'account_name' => $accountName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not verify account: ' . $e->getMessage(),
            ], 422);
        }
    }
}
