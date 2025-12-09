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

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * @throws ConnectionException
     */
    public function chat($messages, $model = 'gpt-4'): string
    {
        $requestData = [
            'model' => $model,
            'input' => $messages,
        ];

        Log::info('ChatGPTService request', [
            'model' => $model,
            'message_count' => count($messages),
            'first_message_preview' => isset($messages[0]['content']) ? substr($messages[0]['content'], 0, 150) : 'none'
        ]);

        $response = Http::withToken($this->apiKey)
            ->post($this->textEndpoint, $requestData);

        if ($response->successful()) {
            $responseData = $response->json();

            Log::info('ChatGPTService response', [
                'has_output' => isset($responseData['output']),
                'response_keys' => array_keys($responseData),
            ]);

            // Extract content from v1/responses format
            $content = $this->extractContent($responseData);

            if (empty($content)) {
                Log::error('Empty content extracted from response', [
                    'full_response' => json_encode($responseData)
                ]);
                throw new \RuntimeException("Empty response from OpenAI API");
            }

            return $content;
        }

        throw new \RuntimeException("OpenAI API Error: " . $response->body());
    }

    /**
     * Extract content from the response data
     */
    private function extractContent(array $responseData): string
    {
        if (!isset($responseData['output'])) {
            return '';
        }

        $output = $responseData['output'];

        // Handle direct string
        if (is_string($output)) {
            return $output;
        }

        // Handle array
        if (is_array($output)) {
            // Array of strings
            if (isset($output[0]) && is_string($output[0])) {
                return implode("\n", $output);
            }

            // Nested content structure with output_text type
            if (isset($output[0]['content']) && is_array($output[0]['content'])) {
                $content = $output[0]['content'];

                if (is_string($content)) {
                    return $content;
                }

                if (is_array($content)) {
                    $parts = [];
                    foreach ($content as $item) {
                        if (is_string($item)) {
                            $parts[] = $item;
                        } elseif (isset($item['type']) && $item['type'] === 'output_text' && isset($item['text'])) {
                            // Handle output_text type from v1/responses API
                            $parts[] = $item['text'];
                        } elseif (isset($item['text'])) {
                            $parts[] = $item['text'];
                        }
                    }
                    return implode("\n", array_filter($parts));
                }
            }

            // Direct text field
            if (isset($output[0]['text'])) {
                return $output[0]['text'];
            }
        }

        return '';
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
        logInfo("Let generate some image". $response);
        if ($response->successful()) {
            return $response->json()['data'];
        }
        logInfo("Let generate some image". $response->json());

        throw new \Exception("OpenAI API Error: " . $response->body());
    }

    /**
     * Format messages array into a single prompt string for responses API
     */
    private function formatMessagesForResponses($messages): string
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
