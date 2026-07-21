<?php

namespace App\BookShop\Services;

use GuzzleHttp\Client;

/**
 * Deliberately a fresh implementation in the BookShop namespace rather
 * than a dependency on App\Services\PaystackService - that class itself
 * has no host-model coupling (it's a pure HTTP wrapper), but depending
 * on it directly would still mean BookShop can't be extracted without
 * also carrying along an unrelated part of the host app's App\Services
 * tree. Reads the SAME config('services.paystack.*') keys the host app
 * already uses, though, since "reuse Paystack" means the same merchant
 * account/credentials, not a second one to configure. See SETUP.md for
 * how to point this at a separate Paystack account instead, if ever
 * needed.
 */
class PaystackService
{
    protected ?Client $client = null;

    protected ?string $secretKey = null;

    public function createSubAccount(array $data): array
    {
        return $this->post('/subaccount', $data);
    }

    public function updateSubAccount(string $subaccountCode, array $data): array
    {
        return $this->put("/subaccount/{$subaccountCode}", $data);
    }

    public function initializeTransaction(array $data): array
    {
        return $this->post('/transaction/initialize', $data);
    }

    public function verifyTransaction(string $reference): array
    {
        return $this->get("/transaction/verify/{$reference}");
    }

    /**
     * Banks for a given country, used to populate a proper bank picker
     * on the branch payment-setup form instead of asking a superadmin to
     * already know Paystack's numeric bank codes by heart.
     */
    public function listBanks(string $country = 'ghana'): array
    {
        return $this->get('/bank?country='.$country.'&currency=GHS');
    }

    private function post(string $path, array $data): array
    {
        $response = $this->client()->post($path, ['json' => $data]);

        return json_decode($response->getBody(), true);
    }

    private function put(string $path, array $data): array
    {
        $response = $this->client()->put($path, ['json' => $data]);

        return json_decode($response->getBody(), true);
    }

    private function get(string $path): array
    {
        $response = $this->client()->get($path);

        return json_decode($response->getBody(), true);
    }

    private function client(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $this->secretKey = config('services.paystack.env') === 'production'
            ? config('services.paystack.production.secret_key')
            : config('services.paystack.development.secret_key');

        if (empty($this->secretKey)) {
            throw new \RuntimeException(
                'Paystack secret key is not configured (services.paystack.*). '.
                'BookShop payments reuse the same Paystack config as the rest of the app - see SETUP.md.'
            );
        }

        return $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer '.$this->secretKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
