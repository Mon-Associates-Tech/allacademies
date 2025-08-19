<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatGPTService
{
    protected $apiKey;
    protected $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function chat($messages, $model = 'gpt-4')
    {
        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, [
                'model' => $model,
                'messages' => $messages,
            ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        throw new \Exception("OpenAI API Error: " . $response->body());
    }
}
