<?php

namespace App\Jobs;

use App\Models\PayrollAuditLog;
use App\Models\PayrollRun;
use App\Notifications\PayrollDisbursementFailed;
use App\Services\PayrollDisbursementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayrollRun implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PayrollRun $run
    ) {}

    public function handle(PayrollDisbursementService $service): void
    {
        try {
            $service->processRun($this->run);
        } catch (\Exception $e) {
            Log::error('Payroll run processing failed', [
                'run_id' => $this->run->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->run->update(['status' => 'failed']);
            
            PayrollAuditLog::logAction('run_processing_failed', $this->run, [
                'error' => $e->getMessage(),
            ]);
            
            $admins = $this->run->school->users()->where('role', 'admin')->get();
            $accountants = $this->run->school->users()->where('role', 'accountant')->get();
            
            foreach ($admins->merge($accountants) as $user) {
                $user->notify(new PayrollDisbursementFailed($this->run));
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessPayrollRun job failed', [
            'run_id' => $this->run->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
