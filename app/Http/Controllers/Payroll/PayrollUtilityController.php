<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Services\PaystackTransferService;

class PayrollUtilityController extends Controller
{
    public function __construct(
        protected PaystackTransferService $paystackService
    ) {}

    public function banks()
    {
        try {
            $banks = $this->paystackService->fetchBanks();
            
            return response()->json([
                'success' => true,
                'banks' => $banks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch banks: ' . $e->getMessage(),
            ], 500);
        }
    }
}
