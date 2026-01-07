<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Log;

trait ResponseExtraction
{
    /**
     * Extract content from OpenAI responses API format
     * This method takes precedence for all response extraction
     */
    protected function extractContentFromResponsesAPI(array $responseData): string
    {
        if (! isset($responseData['output'])) {
            Log::warning('No output field in response');

            return '';
        }

        $output = $responseData['output'];

        // Handle direct string output
        if (is_string($output)) {
            return $output;
        }

        // Handle array output - NEW APPROACH
        if (is_array($output)) {
            // PRIORITY 1: Look for content array in first output element
            if (isset($output[0]['content'])) {
                $fullText = '';

                foreach ($output as $outputItem) {
                    if (isset($outputItem['content']) && is_array($outputItem['content'])) {
                        foreach ($outputItem['content'] as $contentPart) {
                            // Extract text from various possible structures
                            if (is_string($contentPart)) {
                                $fullText .= $contentPart;
                            } elseif (isset($contentPart['text'])) {
                                $fullText .= $contentPart['text'];
                            } elseif (isset($contentPart['type']) && $contentPart['type'] === 'output_text' && isset($contentPart['text'])) {
                                $fullText .= $contentPart['text'];
                            }
                        }
                    }
                }

                if (! empty($fullText)) {
                    return $fullText;
                }
            }

            // PRIORITY 2: Array of strings
            if (isset($output[0]) && is_string($output[0])) {
                return implode('', $output);
            }

            // PRIORITY 3: Direct text field
            if (isset($output[0]['text'])) {
                return $output[0]['text'];
            }
        }

        // Final fallback
        Log::error('Could not extract content from response', [
            'output_structure' => json_encode($output),
        ]);

        return $this->extractTextFromResponse($responseData);
    }

    /**
     * Extract text from various response formats
     */
    private function extractTextFromResponse($response): string
    {
        if (is_string($response)) {
            return $response;
        }

        if (is_array($response)) {
            // Handle Responses API format
            if (isset($response[0]['content'][0]['text'])) {
                return $response[0]['content'][0]['text'];
            }

            // Handle chat completions format
            if (isset($response['output'][0]['content']['text'])) {
                return $response['output'][0]['content']['text'];
            }

            // Handle other possible formats
            if (isset($response['content'])) {
                return is_array($response['content']) ?
                    ($response['content'][0]['text'] ?? json_encode($response['content'])) :
                    $response['content'];
            }
        }

        return (string) $response;
    }

    /**
     * Normalize text response from various formats
     */
    protected function normalizeTextResponse($content): array
    {
        $raw = $content;
        $textSegments = [];

        if (is_string($content)) {
            $textSegments[] = $content;
        } elseif (is_array($content)) {
            foreach ($content as $item) {
                if (is_string($item)) {
                    $textSegments[] = $item;

                    continue;
                }

                if (isset($item['content'])) {
                    $segments = $item['content'];

                    if (is_array($segments)) {
                        foreach ($segments as $segment) {
                            if (is_string($segment)) {
                                $textSegments[] = $segment;

                                continue;
                            }

                            if (isset($segment['text']) && is_string($segment['text'])) {
                                $textSegments[] = $segment['text'];
                            }
                        }
                    } elseif (is_string($segments)) {
                        $textSegments[] = $segments;
                    }
                } elseif (isset($item['text']) && is_string($item['text'])) {
                    $textSegments[] = $item['text'];
                }
            }
        }

        $text = trim(implode("\n\n", array_filter($textSegments, static fn ($segment) => trim($segment) !== '')));

        if ($text === '' && is_array($content)) {
            $text = trim(json_encode($content));
        }

        return [
            'text' => $text,
            'raw' => $raw,
        ];
    }

    /**
     * Check if a command is available on the system
     */
    protected function isCommandAvailable(string $command): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $output = [];
        $returnCode = 0;
        @exec("which {$command} 2>&1", $output, $returnCode);

        return $returnCode === 0;
    }
}
