<?php

namespace App\Services;

use App\Models\Subaccount;
use Exception;
use Illuminate\Database\Eloquent\Model;

class SubaccountPaymentService
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Create a new subaccount for a model
     *
     * @param Model $model
     * @param array $bankData
     * @param array $contactData
     * @param int $percentageCharge
     * @param bool $setPrimary
     * @param string $name
     * @return Subaccount
     * @throws Exception
     */
    public function createSubAccount(
        Model $model,
        array $bankData,
        array $contactData,
        int $percentageCharge = 0,
        bool $setPrimary = true,
        string $name = ''
    ): Subaccount {
        if (empty($bankData['bank_code']) || empty($bankData['account_number'])) {
            throw new Exception('Bank code and account number are required.');
        }

        if (empty($contactData['email'])) {
            throw new Exception('Contact email is required.');
        }

        $subaccountData = [
            'business_name' => $bankData['business_name'] ?? $model->name,
            'bank_code' => $bankData['bank_code'],
            'account_number' => $bankData['account_number'],
            'percentage_charge' => $percentageCharge,
            'description' => $bankData['description'] ?? "Subaccount for {$model->name}",
            'primary_contact_name' => $contactData['name'] ?? $model->name,
            'primary_contact_email' => $contactData['email'],
            'primary_contact_phone' => $contactData['phone'] ?? '',
        ];

        $response = $this->paystack->createSubAccount($subaccountData);

        if (!isset($response['status']) || !$response['status']) {
            throw new Exception($response['message'] ?? 'Failed to create subaccount on Paystack.');
        }

        // If this will be primary, deactivate other primaries
        if ($setPrimary) {
            $model->subaccounts()
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        $subaccount = $model->subaccounts()->create([
            'subaccount_code' => $response['data']['subaccount_code'],
            'name' => $name ?: null,
            'business_name' => $response['data']['business_name'] ?? $subaccountData['business_name'],
            'settlement_bank' => $bankData['settlement_bank'] ?? '',
            'bank_code' => $bankData['bank_code'],
            'account_number' => $response['data']['account_number'] ?? $bankData['account_number'],
            'percentage_charge' => $response['data']['percentage_charge'] ?? $percentageCharge,
            'description' => $response['data']['description'] ?? null,
            'paystack_response' => $response['data'],
            'is_primary' => $setPrimary,
            'status' => 'active',
        ]);

        return $subaccount;
    }

    /**
     * Update an existing subaccount
     */
    public function updateSubAccount(
        Subaccount $subaccount,
        array $bankData,
        array $contactData = []
    ): Subaccount {
        $updateData = [
            'business_name' => $bankData['business_name'] ?? $subaccount->business_name,
            'account_number' => $bankData['account_number'] ?? $subaccount->account_number,
        ];

        $response = $this->paystack->updateSubAccount($subaccount->subaccount_code, $updateData);

        if (!isset($response['status']) || !$response['status']) {
            throw new Exception($response['message'] ?? 'Failed to update subaccount on Paystack.');
        }

        $subaccount->update([
            'business_name' => $updateData['business_name'],
            'settlement_bank' => $bankData['settlement_bank'] ?? $subaccount->settlement_bank,
            'bank_code' => $bankData['bank_code'] ?? $subaccount->bank_code,
            'account_number' => $updateData['account_number'],
            'name' => $bankData['name'] ?? $subaccount->name,
            'paystack_response' => $response['data'] ?? null,
        ]);

        return $subaccount;
    }

    /**
     * Delete/deactivate a subaccount
     */
    public function deleteSubAccount(Subaccount $subaccount, bool $hardDelete = false): bool
    {
        $model = $subaccount->subaccountable;

        // If this was primary and deleting, set another as primary
        if ($subaccount->is_primary) {
            $newPrimary = $model->subaccounts()
                ->where('id', '!=', $subaccount->id)
                ->where('status', 'active')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        if ($hardDelete) {
            return $subaccount->delete();
        }

        return $model->deactivateSubaccount($subaccount);
    }

    /**
     * Get or create a subaccount for a model
     */
    public function getOrCreateSubAccount(
        Model $model,
        array $bankData,
        array $contactData,
        int $percentageCharge = 0
    ): Subaccount {
        $existingSubaccount = $model->primarySubaccount();

        if ($existingSubaccount) {
            return $existingSubaccount;
        }

        return $this->createSubAccount($model, $bankData, $contactData, $percentageCharge);
    }

    /**
     * Get subaccount for transaction
     */
    public function getSubAccountForTransaction(
        Model $model,
        ?string $subaccountCode = null
    ): ?Subaccount {
        if ($subaccountCode) {
            return $model->getSubaccountByCode($subaccountCode);
        }

        return $model->primarySubaccount();
    }

    /**
     * Prepare payment data with subaccount
     */
    public function preparePaymentDataWithSubaccount(
        array $paymentData,
        ?Subaccount $subaccount = null,
        string $chargeBearer = 'account'
    ): array {
        if (!$subaccount || !$subaccount->subaccount_code) {
            return $paymentData;
        }

        $paymentData['subaccount'] = $subaccount->subaccount_code;
        $paymentData['bearer'] = $chargeBearer;

        $paymentData['metadata'] = array_merge(
            $paymentData['metadata'] ?? [],
            [
                'platform_percentage' => $subaccount->percentage_charge,
                'beneficiary_percentage' => 100 - $subaccount->percentage_charge,
                'subaccount_code' => $subaccount->subaccount_code,
                'subaccount_name' => $subaccount->name,
            ]
        );

        return $paymentData;
    }

    /**
     * Calculate revenue split
     */
    public function calculateRevenueSplit(float $totalAmount, Subaccount $subaccount): array
    {
        $platformAmount = ($totalAmount * $subaccount->percentage_charge) / 100;
        $beneficiaryAmount = $totalAmount - $platformAmount;

        return [
            'total' => $totalAmount,
            'platform' => $platformAmount,
            'beneficiary' => $beneficiaryAmount,
            'platform_percentage' => $subaccount->percentage_charge,
            'beneficiary_percentage' => 100 - $subaccount->percentage_charge,
        ];
    }

    /**
     * Validate subaccount
     */
    public function isSubAccountValid(Subaccount $subaccount): bool
    {
        return !empty($subaccount->subaccount_code)
            && !empty($subaccount->bank_code)
            && !empty($subaccount->account_number)
            && $subaccount->status === 'active';
    }

    /**
     * Check if model has valid primary subaccount
     */
    public function modelHasValidSubAccount(Model $model): bool
    {
        $subaccount = $model->primarySubaccount();

        if (!$subaccount) {
            return false;
        }

        return $this->isSubAccountValid($subaccount);
    }

    /**
     * Get valid primary subaccount
     */
    public function getValidSubAccount(Model $model): ?Subaccount
    {
        $subaccount = $model->primarySubaccount();

        if ($subaccount && $this->isSubAccountValid($subaccount)) {
            return $subaccount;
        }

        return null;
    }
}