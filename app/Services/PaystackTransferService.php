<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class PaystackTransferService
{
    protected Client $client;
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->client = new Client([
            'base_uri' => config('services.paystack.base_url', 'https://api.paystack.co'),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    public function fetchBanks(): array
    {
        return Cache::remember('paystack_banks', now()->addDays(7), function () {
            $response = $this->client->get('/bank?currency=NGN');
            $data = json_decode($response->getBody(), true);
            return $data['data'] ?? [];
        });
    }

    public function resolveAccountName(string $accountNumber, string $bankCode): string
    {
        $response = $this->client->get("/bank/resolve?account_number={$accountNumber}&bank_code={$bankCode}");
        $data = json_decode($response->getBody(), true);
        
        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Failed to resolve account');
        }
        
        return $data['data']['account_name'];
    }

    public function createRecipient(string $name, string $accountNumber, string $bankCode): string
    {
        $response = $this->client->post('/transferrecipient', [
            'json' => [
                'type' => 'nuban',
                'name' => $name,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'currency' => 'NGN',
            ],
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Failed to create recipient');
        }
        
        return $data['data']['recipient_code'];
    }

    public function initiateTransfer(string $recipientCode, int $amountKobo, string $reference, string $reason): array
    {
        $response = $this->client->post('/transfer', [
            'json' => [
                'source' => 'balance',
                'amount' => $amountKobo,
                'recipient' => $recipientCode,
                'reference' => $reference,
                'reason' => $reason,
            ],
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Failed to initiate transfer');
        }
        
        return $data['data'];
    }

    public function initiateBulkTransfer(array $transfers): array
    {
        $response = $this->client->post('/transfer/bulk', [
            'json' => [
                'source' => 'balance',
                'transfers' => $transfers,
            ],
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Failed to initiate bulk transfer');
        }
        
        return $data['data'];
    }

    public function verifyTransfer(string $reference): array
    {
        $response = $this->client->get("/transfer/verify/{$reference}");
        $data = json_decode($response->getBody(), true);
        
        if (!$data['status']) {
            throw new \Exception($data['message'] ?? 'Failed to verify transfer');
        }
        
        return $data['data'];
    }

    public function convertToKobo(float $naira): int
    {
        return (int) ($naira * 100);
    }

    public function convertToNaira(int $kobo): float
    {
        return $kobo / 100;
    }
}
