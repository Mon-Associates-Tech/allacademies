<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    protected mixed $apiKey;
    protected string $textEndpoint = 'https://api.openai.com/v1/responses';
    protected string $imageEndpoint = 'https://api.openai.com/v1/images/generations';
    protected TokenUsageService $tokenUsageService;

    public function __construct(TokenUsageService $tokenUsageService)
    {
        $this->apiKey = config('services.openai.key') ?? config('openai.openai.api_key');
        $this->tokenUsageService = $tokenUsageService;
    }

    /**
     * Unified Chat method that handles HTTP, extraction, and usage logging
     */
    public function chat($messages, $model = 'gpt-4', array $options = []): array
    {
        $user = auth()->user();

        $requestData = array_merge([
            'model' => $model,
            'input' => $messages,
        ], $options);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(config('openai.openai.timeout', 90))
                ->post($this->textEndpoint, $requestData);

            if (!$response->successful()) {
                throw new \RuntimeException("OpenAI API Error: " . $response->body());
            }

            $responseData = $response->json();
            $usage = $responseData['usage'] ?? null;

            // Handle usage logging
            if ($user && $usage) {
                $this->tokenUsageService->logUsage(
                    $user,
                    $usage,
                    $options['request_type'] ?? 'chat',
                    $model
                );
            }

            return [
                'success' => true,
                'content' => $this->extractContent($responseData),
                'usage' => $usage,
                'model' => $responseData['model'] ?? $model
            ];

        } catch (\Exception $e) {
            Log::error('ChatGPTService Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Unified Image Generation method
     */
    public function generateImage($prompt, $model = 'dall-e-3', array $options = []): array
    {
        $user = auth()->user();

        try {
            $response = Http::withToken($this->apiKey)
                ->post($this->imageEndpoint, array_merge([
                    'model' => $model,
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                ], $options));

            if (!$response->successful()) {
                throw new \RuntimeException("OpenAI API Error: " . $response->body());
            }

            $responseData = $response->json();
            $images = $responseData['data'] ?? [];

            // Log usage if present in the image response
            if ($user && !empty($images[0]['usage'])) {
                $this->tokenUsageService->logUsage($user, $images[0]['usage'], 'image', $model);
            }

            return [
                'success' => true,
                'images' => $images,
                'model' => $model
            ];
        } catch (\Exception $e) {
            Log::error('Image Generation Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Centralized extraction logic
     */
    public function extractContent(array $responseData): string
    {
        if (!isset($responseData['output'])) {
            return $responseData['choices'][0]['message']['content'] ?? '';
        }

        $output = $responseData['output'];
        if (is_string($output)) return $output;

        if (is_array($output)) {
            if (isset($output[0]['content'])) {
                $text = '';
                foreach ($output as $item) {
                    if (isset($item['content']) && is_array($item['content'])) {
                        foreach ($item['content'] as $part) {
                            $text .= is_array($part) ? ($part['text'] ?? '') : $part;
                        }
                    }
                }
                return $text;
            }
            if (isset($output[0]['text'])) return implode("\n", array_column($output, 'text'));
            return implode("\n", array_filter($output, 'is_string'));
        }

        return '';
    }
}
