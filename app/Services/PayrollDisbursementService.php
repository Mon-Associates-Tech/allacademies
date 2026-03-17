<?php

namespace App\Services;

use App\Models\PayrollAuditLog;
use App\Models\PayrollDisbursement;
use App\Models\PayrollRun;
use App\Notifications\PayrollDisbursementFailed;
use App\Notifications\PayrollRunCompleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayrollDisbursementService
{
    public function __construct(
        protected PaystackTransferService $paystackService
    ) {}

    public function processRun(PayrollRun $run): void
    {
        DB::transaction(function () use ($run) {
            $run->update(['status' => 'processing', 'processed_at' => now()]);
        });
        
        $disbursements = $run->disbursements()
            ->whereIn('status', ['pending', 'failed'])
            ->with(['bankAccount', 'payrollEntry'])
            ->get();
        
        if ($disbursements->count() > 100) {
            $this->processBulkTransfers($run, $disbursements);
        } else {
            $this->processSingularTransfers($run, $disbursements);
        }
        
        $this->updateRunStatus($run);
    }

    protected function processBulkTransfers(PayrollRun $run, $disbursements): void
    {
        $chunks = $disbursements->chunk(100);
        
        foreach ($chunks as $chunk) {
            $transfers = [];
            
            foreach ($chunk as $disbursement) {
                $reference = 'PAY-' . $run->id . '-' . $disbursement->id . '-' . Str::random(8);
                
                $transfers[] = [
                    'amount' => $this->paystackService->convertToKobo($disbursement->amount),
                    'recipient' => $disbursement->bankAccount->paystack_recipient_code,
                    'reference' => $reference,
                    'reason' => "Salary payment for {$disbursement->payrollEntry->full_name}",
                ];
                
                $disbursement->update([
                    'paystack_reference' => $reference,
                    'status' => 'processing',
                ]);
            }
            
            try {
                $response = $this->paystackService->initiateBulkTransfer($transfers);
                $run->update(['paystack_batch_id' => $response['batch_code'] ?? null]);
            } catch (\Exception $e) {
                Log::error('Bulk transfer failed', [
                    'run_id' => $run->id,
                    'error' => $e->getMessage(),
                ]);
                
                foreach ($chunk as $disbursement) {
                    $disbursement->update([
                        'status' => 'failed',
                        'failure_reason' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    protected function processSingularTransfers(PayrollRun $run, $disbursements): void
    {
        foreach ($disbursements as $disbursement) {
            $reference = 'PAY-' . $run->id . '-' . $disbursement->id . '-' . Str::random(8);
            
            try {
                $response = $this->paystackService->initiateTransfer(
                    $disbursement->bankAccount->paystack_recipient_code,
                    $this->paystackService->convertToKobo($disbursement->amount),
                    $reference,
                    "Salary payment for {$disbursement->payrollEntry->full_name}"
                );
                
                $disbursement->update([
                    'paystack_transfer_code' => $response['transfer_code'] ?? null,
                    'paystack_reference' => $reference,
                    'status' => 'processing',
                ]);
            } catch (\Exception $e) {
                Log::error('Transfer failed', [
                    'disbursement_id' => $disbursement->id,
                    'error' => $e->getMessage(),
                ]);
                
                $disbursement->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage(),
                ]);
                
                PayrollAuditLog::logAction('disbursement_failed', $disbursement, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function updateRunStatus(PayrollRun $run): void
    {
        $run->refresh();
        $disbursements = $run->disbursements;
        
        $allSuccess = $disbursements->every(fn($d) => $d->status === 'success');
        $allFailed = $disbursements->every(fn($d) => $d->status === 'failed');
        $anyProcessing = $disbursements->contains(fn($d) => $d->status === 'processing');
        
        if ($anyProcessing) {
            return;
        }
        
        if ($allSuccess) {
            $run->update(['status' => 'completed']);
            
            $admins = $run->school->users()->where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PayrollRunCompleted($run));
            }
        } elseif ($allFailed) {
            $run->update(['status' => 'failed']);
        } else {
            $run->update(['status' => 'completed']);
        }
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];
        
        if (!in_array($event, ['transfer.success', 'transfer.failed', 'transfer.reversed'])) {
            return;
        }
        
        $reference = $data['reference'] ?? null;
        if (!$reference) {
            return;
        }
        
        $disbursement = PayrollDisbursement::where('paystack_reference', $reference)->first();
        if (!$disbursement || $disbursement->isTerminal()) {
            return;
        }
        
        DB::transaction(function () use ($disbursement, $event, $data) {
            $status = match($event) {
                'transfer.success' => 'success',
                'transfer.failed' => 'failed',
                'transfer.reversed' => 'reversed',
            };
            
            $disbursement->update([
                'status' => $status,
                'transferred_at' => $status === 'success' ? now() : null,
                'failure_reason' => $status === 'failed' ? ($data['reason'] ?? 'Unknown') : null,
            ]);
            
            PayrollAuditLog::logAction("disbursement_{$status}", $disbursement, [
                'paystack_event' => $event,
            ]);
            
            if ($status === 'failed') {
                $admins = $disbursement->school->users()->where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new PayrollDisbursementFailed($disbursement));
                }
            }
            
            $this->updateRunStatus($disbursement->payrollRun);
        });
    }
}
