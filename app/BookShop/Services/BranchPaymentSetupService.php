<?php

namespace App\BookShop\Services;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\Branch;
use App\BookShop\Models\BranchPaymentAccount;
use App\BookShop\Models\Staff;
use Illuminate\Support\Facades\Log;

class BranchPaymentSetupService
{
    public function __construct(private readonly PaystackService $paystack)
    {
    }

    /**
     * @param  array{business_name: string, bank_code: string, account_number: string, percentage_charge?: float}  $bankDetails
     *
     * @throws OrderPlacementException
     */
    public function createOrUpdateSubaccount(Branch $branch, array $bankDetails, Staff $actingStaff): BranchPaymentAccount
    {
        $this->validate($bankDetails);

        $account = $branch->paymentAccount ?? new BranchPaymentAccount(['branch_id' => $branch->id]);

        $payload = [
            'business_name' => $bankDetails['business_name'],
            'settlement_bank' => $bankDetails['bank_code'],
            'account_number' => $bankDetails['account_number'],
            'percentage_charge' => $bankDetails['percentage_charge'] ?? 0,
            'primary_contact_email' => $branch->email ?: null,
        ];

        try {
            $response = $account->exists && $account->subaccount_code
                ? $this->paystack->updateSubAccount($account->subaccount_code, $payload)
                : $this->paystack->createSubAccount($payload);
        } catch (\Throwable $e) {
            Log::error('BookShop: Paystack subaccount request failed', [
                'branch_id' => $branch->id,
                'error' => $e->getMessage(),
            ]);

            throw new OrderPlacementException('Could not reach Paystack. Please try again in a moment.');
        }

        if (empty($response['status']) || ! $response['status']) {
            Log::error('BookShop: Paystack subaccount creation/update rejected', [
                'branch_id' => $branch->id,
                'response' => $response,
            ]);

            throw new OrderPlacementException($response['message'] ?? 'Paystack rejected this bank account. Double-check the details and try again.');
        }

        $account->fill([
            'business_name' => $bankDetails['business_name'],
            'settlement_bank' => $bankDetails['settlement_bank_name'] ?? $account->settlement_bank,
            'bank_code' => $bankDetails['bank_code'],
            'account_number' => $bankDetails['account_number'],
            'percentage_charge' => $bankDetails['percentage_charge'] ?? 0,
            'subaccount_code' => $response['data']['subaccount_code'] ?? $account->subaccount_code,
            'paystack_response' => $response['data'] ?? null,
            'is_active' => true,
            'updated_by_staff_id' => $actingStaff->id,
        ]);
        $account->save();

        return $account;
    }

    public function deactivate(Branch $branch, Staff $actingStaff): void
    {
        $branch->paymentAccount?->update([
            'is_active' => false,
            'updated_by_staff_id' => $actingStaff->id,
        ]);
    }

    /**
     * @throws OrderPlacementException
     */
    private function validate(array $details): void
    {
        foreach (['business_name', 'bank_code', 'account_number'] as $field) {
            if (empty($details[$field])) {
                throw new OrderPlacementException("Missing required field: {$field}");
            }
        }

        if (strlen((string) $details['account_number']) < 10) {
            throw new OrderPlacementException('That account number looks too short - double-check it.');
        }
    }
}
