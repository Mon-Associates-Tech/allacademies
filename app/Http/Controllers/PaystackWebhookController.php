<?php

namespace App\Http\Controllers;

use App\Services\PayrollDisbursementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handleTransferWebhook(Request $request, PayrollDisbursementService $service)
    {
        $signature = $request->header('X-Paystack-Signature');
        $body = $request->getContent();
        
        $computedSignature = hash_hmac('sha512', $body, config('services.paystack.secret_key'));
        
        if ($signature !== $computedSignature) {
            Log::warning('Invalid Paystack webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        $payload = json_decode($body, true);
        
        try {
            $service->handleWebhook($payload);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['status' => 'success'], 200);
        }
    }
}
