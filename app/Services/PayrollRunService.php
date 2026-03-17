<?php

namespace App\Services;

use App\Jobs\ProcessPayrollRun;
use App\Models\PayrollAuditLog;
use App\Models\PayrollDisbursement;
use App\Models\PayrollRun;
use App\Models\PayrollSchedule;
use App\Models\User;
use App\Notifications\PayrollRunApproved;
use App\Notifications\PayrollRunSubmittedForApproval;
use Illuminate\Support\Facades\DB;

class PayrollRunService
{
    public function createDraftRun(PayrollSchedule $schedule, array $entryIds, User $initiator, string $runType = 'immediate'): PayrollRun
    {
        return DB::transaction(function () use ($schedule, $entryIds, $initiator, $runType) {
            $entries = $schedule->school->payrollEntries()
                ->whereIn('id', $entryIds)
                ->where('status', 'active')
                ->with('bankAccount')
                ->get();
            
            $totalAmount = $entries->sum('gross_salary');
            
            $run = PayrollRun::create([
                'school_id' => $schedule->school_id,
                'payroll_schedule_id' => $schedule->id,
                'run_type' => $runType,
                'initiated_by' => $initiator->id,
                'status' => 'draft',
                'total_amount' => $totalAmount,
                'recipient_count' => $entries->count(),
            ]);
            
            foreach ($entries as $entry) {
                if (!$entry->bankAccount || !$entry->bankAccount->is_verified) {
                    continue;
                }
                
                PayrollDisbursement::create([
                    'school_id' => $schedule->school_id,
                    'payroll_run_id' => $run->id,
                    'payroll_entry_id' => $entry->id,
                    'bank_account_id' => $entry->bankAccount->id,
                    'amount' => $entry->gross_salary,
                    'status' => 'pending',
                ]);
            }
            
            PayrollAuditLog::logAction('run_created', $run, [
                'initiated_by' => $initiator->name,
                'recipient_count' => $entries->count(),
                'total_amount' => $totalAmount,
            ]);
            
            return $run;
        });
    }

    public function submitForApproval(PayrollRun $run, User $submitter): void
    {
        DB::transaction(function () use ($run, $submitter) {
            $run->update(['status' => 'pending_approval']);
            
            PayrollAuditLog::logAction('run_submitted_for_approval', $run, [
                'submitted_by' => $submitter->name,
            ]);
            
            $admins = User::where('school_id', $run->school_id)
                ->where('role', 'admin')
                ->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new PayrollRunSubmittedForApproval($run));
            }
        });
    }

    public function approveRun(PayrollRun $run, User $approver): void
    {
        if ($run->initiated_by === $approver->id) {
            throw new \Exception('You cannot approve a payroll run you initiated.');
        }
        
        DB::transaction(function () use ($run, $approver) {
            $run->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);
            
            PayrollAuditLog::logAction('run_approved', $run, [
                'approved_by' => $approver->name,
            ]);
            
            $run->initiator->notify(new PayrollRunApproved($run));
            
            ProcessPayrollRun::dispatch($run)->onQueue('payroll');
        });
    }

    public function cancelRun(PayrollRun $run, User $cancelledBy): void
    {
        DB::transaction(function () use ($run, $cancelledBy) {
            $run->update(['status' => 'cancelled']);
            
            PayrollAuditLog::logAction('run_cancelled', $run, [
                'cancelled_by' => $cancelledBy->name,
            ]);
        });
    }

    public function reprocessFailedDisbursements(PayrollRun $run): void
    {
        $failedDisbursements = $run->disbursements()->failed()->get();
        
        if ($failedDisbursements->isEmpty()) {
            return;
        }
        
        DB::transaction(function () use ($run) {
            $run->update(['status' => 'processing']);
            
            PayrollAuditLog::logAction('run_retry_failed', $run, [
                'retried_by' => auth()->user()->name,
            ]);
            
            ProcessPayrollRun::dispatch($run)->onQueue('payroll');
        });
    }
}
