<?php

namespace App\Services;

use App\Models\Subaccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PaymentSetupService
{
    protected PaystackService $paystack;

    const PLATFORM_FEE_PERCENTAGE = 0.01; // 1%

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Create a subaccount for any entity (School, Author, User, etc.)
     */
    public function createSubaccount(Model $entity, array $bankDetails): ?Subaccount
    {
        $this->validateBankDetails($bankDetails);

        try {
            // Prepare data for Paystack
            $paystackData = [
                'business_name' => $bankDetails['business_name'],
                'settlement_bank' => $bankDetails['bank_code'],
                'account_number' => $bankDetails['account_number'],
                'percentage_charge' => $bankDetails['percentage_charge'] ?? 0,
                'description' => $bankDetails['description'] ?? null,
            ];

            // Add primary contact email if available
            if (method_exists($entity, 'getEmailAttribute') || property_exists($entity, 'email')) {
                $paystackData['primary_contact_email'] = $entity->email;
            } elseif ($entity->user ?? null) {
                $paystackData['primary_contact_email'] = $entity->user->email;
            }

            // Create subaccount on Paystack
            $response = $this->paystack->createSubAccount($paystackData);

            if (empty($response['status']) || !$response['status']) {
                Log::error('Paystack subaccount creation failed', [
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id,
                    'response' => $response,
                ]);
                return null;
            }

            // Create local subaccount record
            $subaccount = $entity->subaccount()->create([
                'subaccount_code' => $response['data']['subaccount_code'],
                'business_name' => $bankDetails['business_name'],
                'settlement_bank' => $bankDetails['settlement_bank'] ?? $bankDetails['bank_name'] ?? null,
                'account_number' => $bankDetails['account_number'],
                'bank_code' => $bankDetails['bank_code'],
                'percentage_charge' => $bankDetails['percentage_charge'] ?? 0,
                'description' => $bankDetails['description'] ?? null,
                'paystack_response' => $response['data'],
            ]);

            return $subaccount;

        } catch (\Exception $e) {
            Log::error('Subaccount creation error', [
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing subaccount
     */
    public function updateSubaccount(Model $entity, array $bankDetails): ?Subaccount
    {
        $subaccount = $this->getSubaccount($entity);

        if (!$subaccount) {
            return $this->createSubaccount($entity, $bankDetails);
        }

        try {
            $paystackData = [
                'business_name' => $bankDetails['business_name'] ?? $subaccount->business_name,
                'settlement_bank' => $bankDetails['bank_code'] ?? $subaccount->bank_code,
                'account_number' => $bankDetails['account_number'] ?? $subaccount->account_number,
                'percentage_charge' => $bankDetails['percentage_charge'] ?? $subaccount->percentage_charge,
                'description' => $bankDetails['description'] ?? $subaccount->description,
            ];

            $response = $this->paystack->updateSubAccount($subaccount->subaccount_code, $paystackData);

            if (empty($response['status']) || !$response['status']) {
                Log::error('Paystack subaccount update failed', [
                    'subaccount_code' => $subaccount->subaccount_code,
                    'response' => $response,
                ]);
                return null;
            }

            $subaccount->update([
                'business_name' => $bankDetails['business_name'] ?? $subaccount->business_name,
                'settlement_bank' => $bankDetails['settlement_bank'] ?? $bankDetails['bank_name'] ?? $subaccount->settlement_bank,
                'account_number' => $bankDetails['account_number'] ?? $subaccount->account_number,
                'bank_code' => $bankDetails['bank_code'] ?? $subaccount->bank_code,
                'percentage_charge' => $bankDetails['percentage_charge'] ?? $subaccount->percentage_charge,
                'description' => $bankDetails['description'] ?? $subaccount->description,
                'paystack_response' => array_merge(
                    $subaccount->paystack_response ?? [],
                    ['updated' => $response['data']]
                ),
            ]);

            return $subaccount->fresh();

        } catch (\Exception $e) {
            Log::error('Subaccount update error', [
                'subaccount_id' => $subaccount->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get the subaccount for an entity
     */
    public function getSubaccount(Model $entity): ?Subaccount
    {
        if (!method_exists($entity, 'subaccount')) {
            return null;
        }

        return $entity->subaccount;
    }

    /**
     * Check if an entity has a valid subaccount
     */
    public function hasValidSubaccount(Model $entity): bool
    {
        $subaccount = $this->getSubaccount($entity);
        return $subaccount && !empty($subaccount->subaccount_code);
    }

    /**
     * Validate bank details
     */
    public function validateBankDetails(array $details): void
    {
        $required = ['business_name', 'bank_code', 'account_number'];

        foreach ($required as $field) {
            if (empty($details[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (strlen($details['account_number']) < 10) {
            throw new \InvalidArgumentException("Invalid account number");
        }
    }

    /**
     * Calculate platform fee for a given amount
     */
    public function calculatePlatformFee(float $amount): float
    {
        return round($amount * self::PLATFORM_FEE_PERCENTAGE, 2);
    }

    /**
     * Calculate net amount (what recipient receives after platform fee)
     */
    public function getNetAmount(float $amount, bool $payerCoversFee = false): float
    {
        if ($payerCoversFee) {
            return $amount;
        }
        return $amount - $this->calculatePlatformFee($amount);
    }

    /**
     * Calculate total amount to charge (including platform fee if payer covers it)
     */
    public function getTotalWithFee(float $amount, bool $payerCoversFee = false): float
    {
        if ($payerCoversFee) {
            return $amount + $this->calculatePlatformFee($amount);
        }
        return $amount;
    }

    /**
     * Get platform fee percentage as display string
     */
    public function getPlatformFeePercentageDisplay(): string
    {
        return (self::PLATFORM_FEE_PERCENTAGE * 100) . '%';
    }

    /**
     * Get fee breakdown for display
     */
    public function getFeeBreakdown(float $amount, bool $payerCoversFee = false): array
    {
        $platformFee = $this->calculatePlatformFee($amount);
        $netAmount = $this->getNetAmount($amount, $payerCoversFee);
        $totalCharged = $this->getTotalWithFee($amount, $payerCoversFee);

        return [
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'platform_fee_percentage' => self::PLATFORM_FEE_PERCENTAGE * 100,
            'net_amount' => $netAmount,
            'total_charged' => $totalCharged,
            'payer_covers_fee' => $payerCoversFee,
        ];
    }

    /**
     * Delete a subaccount (local only - Paystack doesn't support deletion)
     */
    public function deleteSubaccount(Model $entity): bool
    {
        $subaccount = $this->getSubaccount($entity);

        if (!$subaccount) {
            return false;
        }

        return $subaccount->delete();
    }
}
