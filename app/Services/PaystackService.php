<?php

namespace App\Services;

use GuzzleHttp\Client;

class PaystackService
{
    protected $client;
    protected $secretKey;
    private $initialized = false;

    public function __construct()
    {
    }

    protected function initialize()
    {
        if ($this->initialized) {
            return;
        }

        // Check if testing mode is enabled AND user has special access
        if (config('services.paystack.env') === 'production') {
            $this->secretKey = config('services.paystack.production.secret_key');
        } else {
            $this->secretKey = config('services.paystack.development.secret_key');
        }

        if ($this->isTestModeEnabled()) {
            // Use test keys for special access users in test mode
            $this->secretKey = config('services.paystack.development.secret_key');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false
        ]);

        $this->initialized = true;
    }

    /**
     * Check if test mode is enabled and user has special access
     */
    protected function isTestModeEnabled(): bool
    {

        // Check if testing mode is enabled via session
        $testMode = session('TESTING_SUBSCRIPTIONS', false);

        if (!$testMode) {
            return false;
        }

        // Get current user email
        $currentUserEmail = auth()->user()?->email;

        if (!$currentUserEmail) {
            return false;
        }

        $specialEmails = special_access_emails() ?: [];
        $isSpecialUser = in_array($currentUserEmail, $specialEmails);

        return $isSpecialUser;
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
        $this->initialize(); // Initialize only when needed

        $response = $this->client->post('/transaction/initialize', [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }

    // 🔹 Verify Transaction
    public function verifyTransaction(string $reference): array
    {
        $this->initialize(); // Initialize only when needed

        $response = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($response->getBody(), true);
    }
}
