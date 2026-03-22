<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollRole;
use Illuminate\Http\Request;

class PayrollRoleController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('can:managePayrollRoles,App\Models\User');
    }

    public function index()
    {
        return view('payroll.roles.index');
    }

    public function create()
    {
        return view('payroll.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $validated['school_id'] = getSchoolId() ?? auth()->user()->school_id;
        
        PayrollRole::create($validated);
        
        return redirect()->route('payroll.roles.index')
            ->with('success', 'Payroll role created successfully.');
    }

    public function edit(PayrollRole $role)
    {
        return view('payroll.roles.edit', compact('role'));
    }

    public function update(Request $request, PayrollRole $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $role->update($validated);
        
        return redirect()->route('payroll.roles.index')
            ->with('success', 'Payroll role updated successfully.');
    }

    public function destroy(PayrollRole $role)
    {
        if ($role->payrollEntries()->exists()) {
            return back()->with('error', 'Cannot delete role with existing payroll entries.');
        }
        
        $role->delete();
        
        return redirect()->route('payroll.roles.index')
            ->with('success', 'Payroll role deleted successfully.');
    }
}
