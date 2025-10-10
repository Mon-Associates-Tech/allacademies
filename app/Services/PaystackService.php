<?php

namespace App\Services;

use GuzzleHttp\Client;

class PaystackService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        if (config('services.paystack.env') === 'production') {
            $this->secretKey = config('services.paystack.production.secret_key');
        } else {
            $this->secretKey = config('services.paystack.development.secret_key');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
            ],
            'verify' => false, // ⚠️ only for local dev
        ]);
    }

    // 🔹 Create Subaccount
    public function createSubAccount(array $data): array
    {
        $response = $this->client->post('/subaccount', [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }

    // 🔹 Initialize Transaction
    public function initializeTransaction(array $data): array
    {
        $response = $this->client->post('/transaction/initialize', [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }

    // 🔹 Verify Transaction
    public function verifyTransaction(string $reference): array
    {
        $response = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($response->getBody(), true);
    }
}
