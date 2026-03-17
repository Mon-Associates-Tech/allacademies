<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\PayrollAuditLog;
use App\Models\PayrollEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayrollEntryService
{
    public function __construct(
        protected PaystackTransferService $paystackService
    ) {}

    public function createEntry(array $data, User $createdBy): PayrollEntry
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $data['created_by'] = $createdBy->id;
            $data['school_id'] = $createdBy->school_id;
            
            $entry = PayrollEntry::create($data);
            
            PayrollAuditLog::logAction('entry_created', $entry, [
                'created_by' => $createdBy->name,
                'gross_salary' => $entry->gross_salary,
            ]);
            
            return $entry;
        });
    }

    public function updateEntry(PayrollEntry $entry, array $data, User $updatedBy): PayrollEntry
    {
        return DB::transaction(function () use ($entry, $data, $updatedBy) {
            $oldData = $entry->only(['gross_salary', 'status', 'first_name', 'last_name']);
            
            $data['updated_by'] = $updatedBy->id;
            $entry->update($data);
            
            PayrollAuditLog::logAction('entry_updated', $entry, [
                'updated_by' => $updatedBy->name,
                'old' => $oldData,
                'new' => $entry->only(['gross_salary', 'status', 'first_name', 'last_name']),
            ]);
            
            return $entry->fresh();
        });
    }

    public function attachBankAccount(PayrollEntry $entry, array $bankData): BankAccount
    {
        return DB::transaction(function () use ($entry, $bankData) {
            $accountName = $this->paystackService->resolveAccountName(
                $bankData['account_number'],
                $bankData['bank_code']
            );
            
            $recipientCode = $this->paystackService->createRecipient(
                $accountName,
                $bankData['account_number'],
                $bankData['bank_code']
            );
            
            $bankAccount = BankAccount::updateOrCreate(
                ['payroll_entry_id' => $entry->id],
                [
                    'school_id' => $entry->school_id,
                    'user_id' => $entry->user_id,
                    'account_name' => $accountName,
                    'account_number' => $bankData['account_number'],
                    'bank_name' => $bankData['bank_name'],
                    'bank_code' => $bankData['bank_code'],
                    'paystack_recipient_code' => $recipientCode,
                    'is_verified' => true,
                ]
            );
            
            PayrollAuditLog::logAction('bank_account_attached', $entry, [
                'bank_name' => $bankData['bank_name'],
                'account_number_masked' => $bankAccount->masked_account_number,
            ]);
            
            return $bankAccount;
        });
    }

    public function deactivateEntry(PayrollEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            $entry->update(['status' => 'inactive']);
            
            PayrollAuditLog::logAction('entry_deactivated', $entry, [
                'deactivated_by' => auth()->user()->name,
            ]);
        });
    }
}
