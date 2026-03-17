<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use App\Models\PayrollSchedule;
use App\Services\PayrollRunService;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function __construct(
        protected PayrollRunService $runService
    ) {
        $this->middleware('can:managePayroll,App\Models\User');
    }

    public function index()
    {
        return view('payroll.runs.index');
    }

    public function show(PayrollRun $run)
    {
        $run->load(['schedule', 'initiator', 'approver', 'disbursements.payrollEntry', 'disbursements.bankAccount']);
        
        return view('payroll.runs.show', compact('run'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payroll_schedule_id' => 'required|exists:payroll_schedules,id',
            'entry_ids' => 'required|array',
            'entry_ids.*' => 'exists:payroll_entries,id',
            'run_type' => 'required|in:immediate,scheduled,recurring',
            'notes' => 'nullable|string',
        ]);
        
        $schedule = PayrollSchedule::findOrFail($validated['payroll_schedule_id']);
        
        $run = $this->runService->createDraftRun(
            $schedule,
            $validated['entry_ids'],
            auth()->user(),
            $validated['run_type']
        );
        
        if ($request->has('notes')) {
            $run->update(['notes' => $validated['notes']]);
        }
        
        return redirect()->route('payroll.runs.show', $run)
            ->with('success', 'Payroll run created successfully.');
    }

    public function submit(PayrollRun $run)
    {
        if ($run->status !== 'draft') {
            return back()->with('error', 'Only draft runs can be submitted.');
        }
        
        $this->runService->submitForApproval($run, auth()->user());
        
        return back()->with('success', 'Payroll run submitted for approval.');
    }

    public function approve(PayrollRun $run)
    {
        $this->authorize('approvePayroll', User::class);
        
        if ($run->status !== 'pending_approval') {
            return back()->with('error', 'Only pending runs can be approved.');
        }
        
        try {
            $this->runService->approveRun($run, auth()->user());
            
            return back()->with('success', 'Payroll run approved and processing started.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(PayrollRun $run)
    {
        if (!in_array($run->status, ['draft', 'pending_approval'])) {
            return back()->with('error', 'Cannot cancel a run that is already processing or completed.');
        }
        
        $this->runService->cancelRun($run, auth()->user());
        
        return back()->with('success', 'Payroll run cancelled.');
    }

    public function retryFailed(PayrollRun $run)
    {
        if ($run->status !== 'failed' && $run->status !== 'completed') {
            return back()->with('error', 'Can only retry failed or completed runs with failures.');
        }
        
        $this->runService->reprocessFailedDisbursements($run);
        
        return back()->with('success', 'Retrying failed disbursements.');
    }

    public function destroy(PayrollRun $run)
    {
        if (!in_array($run->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Can only delete draft or cancelled runs.');
        }
        
        $run->delete();
        
        return redirect()->route('payroll.runs.index')
            ->with('success', 'Payroll run deleted.');
    }
}
