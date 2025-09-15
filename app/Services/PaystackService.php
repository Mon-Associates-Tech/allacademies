<?php
// app/Services/PaystackService.php
namespace App\Services;

use GuzzleHttp\Client;

class PaystackService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        //$secretKey = null;
        if(config('services.paystack.env') === 'production') {
             $this->secretKey = config('services.paystack.production.secret_key');
        } else {

            $this->secretKey = config('services.paystack.development.secret_key');
        }


        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer '. $this->secretKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false // ← Add this line to disable SSL verification
        ]);

    }

    public function initializeTransaction(array $data)
    {
        $response = $this->client->post('/transaction/initialize', [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }

    public function verifyTransaction($reference)
    {
        $response = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($response->getBody(), true);
    }
}
