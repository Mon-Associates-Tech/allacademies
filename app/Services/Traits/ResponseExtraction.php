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
            Log::warning('No output field in response', [
                'response_keys' => array_keys($responseData),
            ]);

            return '';
        }

        $output = $responseData['output'];

        // Handle direct string output
        if (is_string($output)) {
            return $output;
        }

        // Handle array output
        if (is_array($output)) {
            $fullText = '';

            foreach ($output as $outputItem) {
                // Skip non-message items (e.g., function calls, file searches)
                if (!isset($outputItem['type'])) {
                    continue;
                }

                // Handle message type with content array
                if ($outputItem['type'] === 'message' && isset($outputItem['content']) && is_array($outputItem['content'])) {
                    foreach ($outputItem['content'] as $contentPart) {
                        if (is_string($contentPart)) {
                            $fullText .= $fullText ? "\n" . $contentPart : $contentPart;
                        } elseif (is_array($contentPart)) {
                            // Handle output_text type
                            if (isset($contentPart['type']) && $contentPart['type'] === 'output_text' && isset($contentPart['text'])) {
                                $fullText .= $fullText ? "\n" . $contentPart['text'] : $contentPart['text'];
                            }
                            // Handle text field directly
                            elseif (isset($contentPart['text'])) {
                                $fullText .= $fullText ? "\n" . $contentPart['text'] : $contentPart['text'];
                            }
                        }
                    }
                }
                // Handle direct content field (array or string)
                elseif (isset($outputItem['content'])) {
                    if (is_string($outputItem['content'])) {
                        $fullText .= $fullText ? "\n" . $outputItem['content'] : $outputItem['content'];
                    } elseif (is_array($outputItem['content'])) {
                        foreach ($outputItem['content'] as $contentPart) {
                            if (is_string($contentPart)) {
                                $fullText .= $fullText ? "\n" . $contentPart : $contentPart;
                            } elseif (isset($contentPart['text'])) {
                                $fullText .= $fullText ? "\n" . $contentPart['text'] : '';
                            }
                        }
                    }
                }
                // Handle text field directly on output item
                elseif (isset($outputItem['text'])) {
                    $fullText .= $fullText ? "\n" . $outputItem['text'] : $outputItem['text'];
                }
            }

            if (!empty(trim($fullText))) {
                return trim($fullText);
            }

            // Fallback: try to extract from first item
            if (isset($output[0])) {
                $first = $output[0];
                if (is_string($first)) {
                    return $first;
                }
                if (is_array($first)) {
                    // Try content[0].text pattern
                    if (isset($first['content'][0]['text'])) {
                        return $first['content'][0]['text'];
                    }
                    // Try content.text pattern
                    if (isset($first['content']['text'])) {
                        return $first['content']['text'];
                    }
                    // Try text field
                    if (isset($first['text'])) {
                        return $first['text'];
                    }
                }
            }
        }

        // Final fallback - log detailed structure for debugging
        Log::error('Could not extract content from response', [
            'output_structure' => json_encode($output, JSON_PRETTY_PRINT),
            'output_type' => gettype($output),
            'output_count' => is_array($output) ? count($output) : 'N/A',
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

            // Safe fallback: return JSON representation instead of forcing array-to-string
            return json_encode($response, JSON_PRETTY_PRINT);
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
