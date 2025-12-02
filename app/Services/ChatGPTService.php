<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatGPTService
{
    protected mixed $apiKey;
    protected string $textEndpoint = 'https://api.openai.com/v1/responses';
    protected string $imageEndpoint = 'https://api.openai.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function chat($messages, $model = 'gpt-4')
    {
        // Format messages for responses API
        $prompt = $this->formatMessagesForResponses($messages);

        $response = Http::withToken($this->apiKey)
            ->post($this->textEndpoint, [
                'model' => $model,
                'input' => $prompt,
            ]);

        if ($response->successful()) {
            return $response->json()['output'];
        }

        throw new \Exception("OpenAI API Error: " . $response->body());
    }

    public function generateImage($prompt, $model = 'dall-e-3', $n = 1, $size = '1024x1024')
    {
        $response = Http::withToken($this->apiKey)
            ->post($this->imageEndpoint, [
                'model' => $model,
                'prompt' => $prompt,
                'n' => $n,
                'size' => $size,
            ]);

        if ($response->successful()) {
            return $response->json()['data'];
        }

        throw new \Exception("OpenAI API Error: " . $response->body());
    }

    /**
     * Format messages array into a single prompt string for responses API
     */
    private function formatMessagesForResponses($messages)
    {
        $prompt = '';

        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';

            $prompt .= ucfirst($role) . ': ' . $content . "\n\n";
        }

        return trim($prompt);
    }
}
