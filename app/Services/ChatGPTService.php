<?php

namespace App\Services;

use App\Services\Traits\ResponseExtraction;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    use ResponseExtraction;

    protected $model = 'gpt-5';

    protected string $textEndpoint = 'https://api.openai.com/v1/responses';

    protected string $imageEndpoint = 'https://api.openai.com/v1/images/generations';

    protected TokenUsageService $tokenUsageService;

    public function __construct(TokenUsageService $tokenUsageService)
    {
        $this->tokenUsageService = $tokenUsageService;
        $this->model = config('openai.openai.model') ?: config('openai.openai.default_model') ?: 'gpt-5';
    }

    /**
     * Resolve the API key dynamically on each request.
     * In long-running queue workers the constructor runs once at process start,
     * so reading config() there can return a stale value after config:cache.
     */
    protected function resolveApiKey(): ?string
    {
        $key = config('services.openai.key') ?? config('openai.openai.api_key');

        if (empty($key)) {
            Log::error('ChatGPTService: OPENAI_API_KEY is not configured', [
                'services_openai_key' => config('services.openai.key') ? 'present' : 'missing',
                'openai_openai_api_key' => config('openai.openai.api_key') ? 'present' : 'missing',
            ]);
        }

        return $key;
    }

    /**
     * Unified Chat method with retry logic and usage logging
     * Supports both simple message arrays and complex request data
     */
    public function chat($messages, $model = '', array $options = []): array
    {
        // Extract internal options
        $requestType = $options['request_type'] ?? 'chat';
        unset($options['request_type']);

        // Build proper responses API request
        $formattedMessages = is_string($messages) ? [['role' => 'user', 'content' => $messages]] : $messages;

        $requestData = [
            'model' => $model ?: $this->model,
            'input' => $formattedMessages,
        ];

        // Add supported parameters
        if (isset($options['temperature'])) {
            $requestData['temperature'] = $options['temperature'];
        }
        if (isset($options['max_output_tokens'])) {
            $requestData['max_output_tokens'] = (int) $options['max_output_tokens'];
        }

        return $this->sendChatRequest($requestData, ['request_type' => $requestType]);
    }

    /**
     * Central HTTP request handler with retry logic
     * This method is used by both ChatGPTService and ResearchAssistantService
     */
    protected function sendChatRequest(array $requestData, array $options = []): array
    {
        $user = auth()->user();
        $timeout = config('openai.openai.timeout', 60);
        $maxRetries = 1;
        $retryDelay = 2;

        // Extract request_type before sending to API (it's for internal use only)
        $requestType = $options['request_type'] ?? 'chat';
        unset($requestData['request_type']);

        $apiKey = $this->resolveApiKey();

        Log::info('ChatGPTService sendChatRequest', [
            'model' => $requestData['model'] ?? 'default',
            'api_key_present' => ! empty($apiKey),
            'api_key_prefix' => $apiKey ? substr($apiKey, 0, 10) . '...' : 'NULL',
            'endpoint' => $this->textEndpoint,
            'input_message_count' => is_array($requestData['input'] ?? null) ? count($requestData['input']) : 0,
        ]);

        $verifySsl = config('openai.openai.verify_ssl', true);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout($timeout)
                    ->connectTimeout(10)
                    ->withOptions(['verify' => $verifySsl])
                    ->post($this->textEndpoint, $requestData);

                Log::info('ChatGPTService HTTP response received', [
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'body_length' => strlen($response->body()),
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $usage = $responseData['usage'] ?? null;

                    $extractedContent = $this->extractContent($responseData);

                    // If content extraction returned empty, log the raw response for debugging
                    if (empty($extractedContent)) {
                        Log::warning('ChatGPTService: empty content extracted from successful response', [
                            'response_keys' => array_keys($responseData),
                            'has_output' => isset($responseData['output']),
                            'raw_response_preview' => substr(json_encode($responseData), 0, 500),
                        ]);
                    }

                    // Handle usage logging
                    if ($user && $usage) {
                        $this->tokenUsageService->logUsage(
                            $user,
                            $usage,
                            $requestType,
                            $requestData['model'] ?? 'gpt-4'
                        );
                    }

                    return [
                        'success' => true,
                        'content' => $extractedContent,
                        'usage' => $usage,
                        'model' => $responseData['model'] ?? $requestData['model'] ?? 'gpt-5',
                    ];
                }

                if (in_array($response->status(), [429, 503, 502])) {
                    if ($attempt < $maxRetries) {
                        $waitTime = $retryDelay * $attempt;
                        Log::warning('OpenAI API rate limit or unavailable, retrying', [
                            'status' => $response->status(),
                            'attempt' => $attempt,
                            'wait_time' => $waitTime,
                        ]);
                        sleep($waitTime);

                        continue;
                    }
                }

                Log::error('OpenAI API Error', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);

                $status = $response->status();
                $error = match(true) {
                    $status === 401 => 'AI service authentication failed. Please contact support.',
                    $status === 429 => 'AI service is busy. Please try again in a moment.',
                    $status >= 500 => 'AI service is temporarily unavailable. Please try again.',
                    default        => 'AI service error. Please try again.',
                };

                return ['success' => false, 'error' => $error];

            } catch (ConnectionException $e) {
                Log::error('OpenAI Connection Error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                ]);

                if ($attempt < $maxRetries) {
                    $waitTime = $retryDelay * $attempt;
                    sleep($waitTime);

                    continue;
                }

                return [
                    'success' => false,
                    'error' => 'Connection timeout. Please try again.',
                ];

            } catch (\Exception $e) {
                Log::error('ChatGPTService Error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                ]);

                if ($attempt < $maxRetries) {
                    $waitTime = $retryDelay * $attempt;
                    sleep($waitTime);

                    continue;
                }

                return [
                    'success' => false,
                    'error' => 'Service temporarily unavailable. Please try again.',
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Service temporarily unavailable after multiple attempts. Please try again later.',
        ];
    }

    /**
     * Unified Image Generation method
     */
    public function generateImage($prompt, $model = null, array $options = []): array
    {
        if ($model === null) {
            $model = config('openai.models.default_image_model', 'gpt-image-1-mini');
        }

        $user = auth()->user();

        $apiKey = $this->resolveApiKey();

        $verifySsl = config('openai.openai.verify_ssl', true);

        try {
            $response = Http::withToken($apiKey)
                ->withOptions(['verify' => $verifySsl])
                ->post($this->imageEndpoint, array_merge([
                    'model' => $model,
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                ], $options));

            if (! $response->successful()) {
                throw new \RuntimeException('OpenAI API Error: '.$response->body());
            }

            $responseData = $response->json();
            $images = $responseData['data'] ?? [];

            // Normalize image data: gpt-image models return b64_json, DALL-E returns url
            foreach ($images as &$image) {
                if (isset($image['b64_json']) && !isset($image['url'])) {
                    // Convert base64 to a data URL for consistent handling
                    $image['url'] = 'data:image/png;base64,' . $image['b64_json'];
                    $image['is_base64'] = true;
                } else {
                    $image['is_base64'] = false;
                }
            }
            unset($image);

            // Log usage if present in the image response
            $usage = $responseData['usage'] ?? ($images[0]['usage'] ?? null);
            if ($user && $usage) {
                $this->tokenUsageService->logUsage($user, $usage, 'image', $model);
            }

            return [
                'success' => true,
                'images' => $images,
                'model' => $model,
                'usage' => $usage,
            ];
        } catch (\Exception $e) {
            Log::error('Image Generation Error', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Extract content from response data
     * For enhanced extraction with educational context, use ResearchAssistantService
     */
    public function extractContent(array $responseData): string
    {
        return $this->extractContentFromResponsesAPI($responseData);
    }
}
