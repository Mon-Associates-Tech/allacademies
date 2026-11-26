<?php

namespace App\Services;

use App\Services\Traits\ResponseExtraction;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    use ResponseExtraction;

    protected $model = 'gpt-6.2-sol';

    protected string $textEndpoint = 'https://api.openai.com/v1/responses';

    protected string $imageEndpoint = 'https://api.openai.com/v1/images/generations';

    protected TokenUsageService $tokenUsageService;

    public function __construct(TokenUsageService $tokenUsageService)
    {
        $this->tokenUsageService = $tokenUsageService;
        $this->model = config('openai.openai.model') ?: config('openai.openai.default_model') ?: 'gpt-6.2-sol';
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
    public function chat($messages, array $options = []): array
    {
        // Extract internal options
        $requestType = $options['request_type'] ?? 'chat';
        unset($options['request_type']);

        // Build proper responses API request
        $formattedMessages = is_string($messages) ? [['role' => 'user', 'content' => $messages]] : $messages;

        $resolvedModel = $options['model'] ?? $this->model;
        unset($options['model']);

        $requestData = [
            'model' => $resolvedModel,
            'input' => $formattedMessages,
        ];

        // Add supported parameters - some models (e.g. gpt-5) do not support temperature
        if (isset($options['temperature']) && ! $this->modelDoesNotSupportTemperature($resolvedModel)) {
            $requestData['temperature'] = $options['temperature'];
        }
        if (isset($options['max_output_tokens'])) {
            $requestData['max_output_tokens'] = (int) $options['max_output_tokens'];
        }

        return $this->sendChatRequest($requestData, ['request_type' => $requestType]);
    }

    /**
     * Check if the given model does not support the temperature parameter.
     * GPT-5 family models do not support temperature.
     */
    protected function modelDoesNotSupportTemperature(string $model): bool
    {
        // GPT-5 family models do not support temperature
        return str_starts_with($model, 'gpt-5');
    }

    /**
     * Central HTTP request handler with retry logic
     * This method is used by both ChatGPTService and ResearchAssistantService
     */
    protected function sendChatRequest(array $requestData, array $options = []): array
    {
        $user = auth()->user();
        $timeout = config('openai.openai.timeout', 300);
        $maxRetries = 3;
        $retryDelay = 5;

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

                    // Check if the response was incomplete (e.g., hit max_output_tokens)
                    $responseStatus = $responseData['status'] ?? null;
                    $incompleteDetails = $responseData['incomplete_details'] ?? null;
                    $isIncomplete = ($responseStatus === 'incomplete') || !empty($incompleteDetails);

                    if ($isIncomplete) {
                        Log::warning('ChatGPTService: response incomplete', [
                            'status' => $responseStatus,
                            'incomplete_details' => $incompleteDetails,
                            'model' => $requestData['model'] ?? 'default',
                        ]);
                    }

                    // Extract both text and images from the response
                    $extracted = $this->extractContentAndImages($responseData);
                    $extractedContent = $extracted['text'];
                    $extractedImages = $extracted['images'];

                    // If content extraction returned empty, log the raw response for debugging
                    if (empty($extractedContent) && empty($extractedImages)) {
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
                            $requestData['model'] ?? 'gpt-6.2-sol'
                        );
                    }

                    $result = [
                        'success' => true,
                        'content' => $extractedContent,
                        'usage' => $usage,
                        'model' => $responseData['model'] ?? $requestData['model'] ?? 'gpt-6.2-sol',
                    ];

                    // Include images if any were extracted
                    if (!empty($extractedImages)) {
                        $result['images'] = $extractedImages;
                    }

                    // Flag if the response was truncated/incomplete
                    if ($isIncomplete) {
                        $result['incomplete'] = true;
                        $result['incomplete_reason'] = $incompleteDetails['reason'] ?? 'unknown';
                    }

                    return $result;
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
                $errorMessage = $e->getMessage();
                $isTimeout = str_contains($errorMessage, 'timed out') || str_contains($errorMessage, 'Operation timed out');

                Log::warning('OpenAI Connection Error', [
                    'error' => $errorMessage,
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries,
                    'is_timeout' => $isTimeout,
                ]);

                if ($isTimeout && $attempt < $maxRetries) {
                    $waitTime = $retryDelay * ($attempt * 2); // Exponential backoff: 10s, 20s, 40s...
                    Log::info('OpenAI timeout - retrying with exponential backoff', [
                        'wait_seconds' => $waitTime,
                        'next_attempt' => $attempt + 1,
                    ]);
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

    /**
     * Extract both text content and images from response data.
     * Returns ['text' => string, 'images' => array].
     */
    public function extractContentAndImages(array $responseData): array
    {
        return $this->extractContentAndImagesFromResponsesAPI($responseData);
    }

    /**
     * Stream a chat request using OpenAI's SSE streaming API.
     *
     * Calls the callback with each text delta chunk as it arrives,
     * then returns the final result array (content, usage, images, etc.).
     *
     * @param array $requestData The request payload (model, input, etc.)
     * @param callable $chunkCallback Called with each text delta string
     * @param array $options Internal options
     * @return array Final result with 'success', 'content', 'usage', 'images', etc.
     */
    public function streamChat(array $requestData, callable $chunkCallback, array $options = []): array
    {
        return $this->sendStreamChatRequest($requestData, $chunkCallback, $options);
    }

    /**
     * Send a streaming chat request to the OpenAI Responses API.
     * Parses SSE events and calls the callback for each text delta.
     */
    protected function sendStreamChatRequest(array $requestData, callable $chunkCallback, array $options = []): array
    {
        $user = auth()->user();
        $timeout = config('openai.openai.timeout', 300);
        $requestType = $options['request_type'] ?? 'chat';

        $apiKey = $this->resolveApiKey();
        $verifySsl = config('openai.openai.verify_ssl', true);

        // Enable streaming
        $requestData['stream'] = true;

        // Remove temperature for models that don't support it (e.g. gpt-5)
        $model = $requestData['model'] ?? '';
        if ($this->modelDoesNotSupportTemperature($model)) {
            unset($requestData['temperature']);
        }

        Log::info('ChatGPTService streamChatRequest', [
            'model' => $requestData['model'] ?? 'default',
            'api_key_present' => !empty($apiKey),
            'endpoint' => $this->textEndpoint,
        ]);

        try {
            // Use streamWith() to get a streaming response
            $streamResponse = Http::withToken($apiKey)
                ->timeout($timeout)
                ->connectTimeout(10)
                ->withOptions(['verify' => $verifySsl, 'stream' => true])
                ->withHeaders(['Accept' => 'text/event-stream'])
                ->asJson()
                ->withBody('', 'application/json')
                ->send('POST', $this->textEndpoint, [
                    'json' => $requestData,
                ]);

            if (! $streamResponse->successful()) {
                $errorBody = $streamResponse->body();
                Log::error('ChatGPTService streaming API error', [
                    'status' => $streamResponse->status(),
                    'body' => $errorBody,
                ]);

                return [
                    'success' => false,
                    'error' => 'AI service error. Please try again.',
                ];
            }

            // Read the SSE stream line by line
            $content = '';
            $images = [];
            $usage = null;
            $incomplete = false;
            $incompleteReason = null;
            $buffer = '';
            $body = $streamResponse->toPsrResponse()->getBody();
            $currentEventType = null;
            $currentEventData = '';

            $processEvent = function () use (&$currentEventType, &$currentEventData, $chunkCallback, &$content, &$images, &$usage, &$incomplete, &$incompleteReason): void {
                if ($currentEventType && $currentEventData) {
                    $this->processStreamEvent(
                        $currentEventType,
                        $currentEventData,
                        $chunkCallback,
                        $content,
                        $images,
                        $usage,
                        $incomplete,
                        $incompleteReason
                    );
                }

                $currentEventType = null;
                $currentEventData = '';
            };

            $processLine = function (string $line) use (&$currentEventType, &$currentEventData, $processEvent): void {
                $line = rtrim($line, "\r");
                if ($line === '') {
                    $processEvent();
                } elseif (str_starts_with($line, 'event:')) {
                    $currentEventType = trim(substr($line, 6));
                } elseif (str_starts_with($line, 'data:')) {
                    $data = trim(substr($line, 5));
                    $currentEventData .= ($currentEventData === '' ? '' : "\n").$data;
                }
            };

            while (! $body->eof()) {
                $buffer .= $body->read(1024);

                while (($newlinePosition = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $newlinePosition);
                    $buffer = substr($buffer, $newlinePosition + 1);
                    $processLine($line);
                }
            }

            if ($buffer !== '') {
                $processLine($buffer);
            }
            $processEvent();

            // Handle usage logging
            if ($user && $usage) {
                $this->tokenUsageService->logUsage(
                    $user,
                    $usage,
                    $requestType,
                    $requestData['model'] ?? 'gpt-6.2-sol'
                );
            }

            $result = [
                'success' => true,
                'content' => $content,
                'usage' => $usage,
                'model' => $requestData['model'] ?? 'gpt-6.2-sol',
            ];

            if (!empty($images)) {
                $result['images'] = $images;
            }

            if ($incomplete) {
                $result['incomplete'] = true;
                $result['incomplete_reason'] = $incompleteReason;
            }

            return $result;

        } catch (ConnectionException $e) {
            Log::error('ChatGPTService streaming connection error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Connection timeout. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('ChatGPTService streaming error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Service temporarily unavailable. Please try again.',
            ];
        }
    }

    /**
     * Process a single SSE event from the streaming response.
     *
     * @param string $eventType The SSE event type
     * @param string $eventData The JSON data string
     * @param callable $chunkCallback Called with each text delta
     * @param string $content Accumulated content (passed by reference via return)
     * @param array $images Accumulated images
     * @param mixed $usage Usage data
     * @param bool $incomplete Whether response is incomplete
     * @param string $incompleteReason Reason for incompleteness
     * @return array Updated state
     */
    protected function processStreamEvent(
        string $eventType,
        string $eventData,
        callable $chunkCallback,
        string &$content,
        array &$images,
        &$usage,
        bool &$incomplete,
        ?string &$incompleteReason
    ): array {
        // Skip done events
        if ($eventData === '[DONE]') {
            return compact('content', 'images', 'usage', 'incomplete', 'incompleteReason');
        }

        $data = json_decode($eventData, true);
        if (!is_array($data)) {
            return compact('content', 'images', 'usage', 'incomplete', 'incompleteReason');
        }

        $type = $data['type'] ?? $eventType;

        match ($type) {
            'response.output_text.delta' => $this->handleTextDelta($data, $chunkCallback, $content),
            'response.output_image.delta' => $this->handleImageDelta($data, $images),
            'response.completed' => $this->handleResponseCompleted($data, $usage, $incomplete, $incompleteReason, $content, $images),
            'error' => $this->handleStreamError($data),
            default => null,
        };

        return compact('content', 'images', 'usage', 'incomplete', 'incompleteReason');
    }

    protected function handleTextDelta(array $data, callable $chunkCallback, string &$content): void
    {
        $delta = $data['delta'] ?? '';
        if ($delta !== '') {
            $content .= $delta;
            $chunkCallback($delta);
        }
    }

    protected function handleImageDelta(array $data, array &$images): void
    {
        // Collect image data from streaming events
        $content = $data['content'] ?? null;
        if (is_array($content) && isset($content['type'])) {
            $images[] = $content;
        }
    }

    protected function handleResponseCompleted(
        array $data,
        &$usage,
        bool &$incomplete,
        ?string &$incompleteReason,
        string &$content,
        array &$images
    ): void {
        $response = $data['response'] ?? [];

        // Extract usage from completed response
        if (isset($response['usage'])) {
            $usage = $response['usage'];
        }

        // Check if response was incomplete
        $status = $response['status'] ?? null;
        $incompleteDetails = $response['incomplete_details'] ?? null;
        if ($status === 'incomplete' || !empty($incompleteDetails)) {
            $incomplete = true;
            $incompleteReason = $incompleteDetails['reason'] ?? 'unknown';
        }

        // Extract any images from the completed response output
        $output = $response['output'] ?? [];
        if (!empty($output)) {
            $extracted = $this->extractContentAndImagesFromResponsesAPI(['output' => $output]);
            // Only update images if we found new ones
            if (!empty($extracted['images'])) {
                $images = array_merge($images, $extracted['images']);
            }
        }
    }

    protected function handleStreamError(array $data): void
    {
        $message = $data['message'] ?? 'Unknown streaming error';
        Log::error('ChatGPTService streaming error event', ['error' => $message]);
        throw new \RuntimeException('Streaming error: '.$message);
    }
}
