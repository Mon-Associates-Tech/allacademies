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
     * Create a new subaccount for a model (School, Author, etc.)
     *
     * @param Model $model The model to create a subaccount for (School, Author, etc.)
     * @param array $bankData Bank account information
     * @param array $contactData Contact information
     * @param int $percentageCharge Platform commission percentage (default: 0)
     * @return Subaccount
     * @throws Exception
     */
    public function createSubAccount(
        Model $model,
        array $bankData,
        array $contactData,
        int $percentageCharge = 0
    ): Subaccount {
        // Validate required bank data
        if (empty($bankData['bank_code']) || empty($bankData['account_number'])) {
            throw new Exception('Bank code and account number are required.');
        }

        // Validate required contact data
        if (empty($contactData['email'])) {
            throw new Exception('Contact email is required.');
        }

        // Prepare data for Paystack API
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

        // Call Paystack API
        $response = $this->paystack->createSubAccount($subaccountData);

        // Check if response was successful
        if (!isset($response['status']) || !$response['status']) {
            throw new Exception($response['message'] ?? 'Failed to create subaccount on Paystack.');
        }

        // Save subaccount to database using polymorphic relationship
        $subaccount = $model->subaccount()->create([
            'subaccount_code' => $response['data']['subaccount_code'],
            'business_name' => $response['data']['business_name'] ?? $subaccountData['business_name'],
            'settlement_bank' => $bankData['settlement_bank'] ?? '',
            'bank_code' => $bankData['bank_code'],
            'account_number' => $response['data']['account_number'] ?? $bankData['account_number'],
            'percentage_charge' => $response['data']['percentage_charge'] ?? $percentageCharge,
            'description' => $response['data']['description'] ?? null,
            'paystack_response' => $response['data'],
        ]);

        return $subaccount;
    }

    /**
     * Update an existing subaccount
     *
     * @param Subaccount $subaccount The subaccount to update
     * @param array $bankData Updated bank information
     * @param array $contactData Updated contact information
     * @return Subaccount
     * @throws Exception
     */
    public function updateSubAccount(
        Subaccount $subaccount,
        array $bankData,
        array $contactData = []
    ): Subaccount {
        // Prepare update data for Paystack
        $updateData = [
            'business_name' => $bankData['business_name'] ?? $subaccount->business_name,
            'account_number' => $bankData['account_number'] ?? $subaccount->account_number,
        ];

        // Call Paystack API to update
        $response = $this->paystack->updateSubAccount($subaccount->subaccount_code, $updateData);

        if (!isset($response['status']) || !$response['status']) {
            throw new Exception($response['message'] ?? 'Failed to update subaccount on Paystack.');
        }

        // Update database record
        $subaccount->update([
            'business_name' => $updateData['business_name'],
            'settlement_bank' => $bankData['settlement_bank'] ?? $subaccount->settlement_bank,
            'bank_code' => $bankData['bank_code'] ?? $subaccount->bank_code,
            'account_number' => $updateData['account_number'],
            'paystack_response' => $response['data'] ?? null,
        ]);

        return $subaccount;
    }

    /**
     * Delete a subaccount from the database
     * Note: Paystack doesn't support deleting subaccounts via API, only database removal
     *
     * @param Subaccount $subaccount
     * @return bool
     */
    public function deleteSubAccount(Subaccount $subaccount): bool
    {
        return $subaccount->delete();
    }

    /**
     * Get or create a subaccount for a model
     * If subaccount already exists, return it; otherwise create a new one
     *
     * @param Model $model
     * @param array $bankData
     * @param array $contactData
     * @param int $percentageCharge
     * @return Subaccount
     * @throws Exception
     */
    public function getOrCreateSubAccount(
        Model $model,
        array $bankData,
        array $contactData,
        int $percentageCharge = 0
    ): Subaccount {
        // Check if subaccount already exists
        $existingSubaccount = $model->subaccount;

        if ($existingSubaccount) {
            return $existingSubaccount;
        }

        // Create new subaccount
        return $this->createSubAccount($model, $bankData, $contactData, $percentageCharge);
    }

    /**
     * Prepare payment data for transaction initialization with subaccount
     *
     * @param array $paymentData Base payment data
     * @param Subaccount|null $subaccount Optional subaccount for split payment
     * @param string $chargeBearer Who bears the Paystack charges: 'account' or 'subaccount'
     * @return array
     */
    public function preparePaymentDataWithSubaccount(
        array $paymentData,
        ?Subaccount $subaccount = null,
        string $chargeBearer = 'account'
    ): array {
        if (!$subaccount || !$subaccount->subaccount_code) {
            return $paymentData;
        }

        // Add subaccount information
        $paymentData['subaccount'] = $subaccount->subaccount_code;

        // Set who bears the transaction charge
        $paymentData['bearer'] = $chargeBearer;

        // Add metadata about the split for tracking
        $paymentData['metadata'] = array_merge(
            $paymentData['metadata'] ?? [],
            [
                'platform_percentage' => $subaccount->percentage_charge,
                'beneficiary_percentage' => 100 - $subaccount->percentage_charge,
                'subaccount_code' => $subaccount->subaccount_code,
            ]
        );

        return $paymentData;
    }

    /**
     * Calculate revenue split between platform and beneficiary
     *
     * @param float $totalAmount Total amount in base currency unit
     * @param Subaccount $subaccount
     * @return array ['total' => amount, 'platform' => amount, 'beneficiary' => amount]
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
     * Validate subaccount configuration
     *
     * @param Subaccount $subaccount
     * @return bool
     */
    public function isSubAccountValid(Subaccount $subaccount): bool
    {
        return !empty($subaccount->subaccount_code)
            && !empty($subaccount->bank_code)
            && !empty($subaccount->account_number);
    }

    /**
     * Check if a model has a valid subaccount configured
     *
     * @param Model $model
     * @return bool
     */
    public function modelHasValidSubAccount(Model $model): bool
    {
        $subaccount = $model->subaccount;

        if (!$subaccount) {
            return false;
        }

        return $this->isSubAccountValid($subaccount);
    }

    /**
     * Get subaccount information with validation
     *
     * @param Model $model
     * @return Subaccount|null
     */
    public function getValidSubAccount(Model $model): ?Subaccount
    {
        $subaccount = $model->subaccount;

        if ($subaccount && $this->isSubAccountValid($subaccount)) {
            return $subaccount;
        }

        return null;
    }
}
