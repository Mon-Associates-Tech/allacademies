<?php

namespace App\Services;

use App\Events\TokenUsageUpdated;
use App\Models\Chat\OpenAiTokenUsageLog;
use DOMDocument;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use ZipArchive;

class AcademicChatService
{
    protected mixed $apiKey;
    protected string $endpoint = 'https://api.openai.com/v1/responses';
    protected string $model = '';

    protected ChatGPTService $chatGPTService;
    protected ModelSelectionService $modelSelectionService;

    public function __construct(ChatGPTService $chatGPTService, ModelSelectionService $modelSelectionService)
    {
        $this->chatGPTService = $chatGPTService;
        $this->modelSelectionService = $modelSelectionService;
        $this->apiKey = config('openai.openai.api_key');
    }

    public function processRequest($parameters, $conversationHistory)
    {
        // First, determine if this request requires image generation
        $modelType = $this->detectModelType($parameters, $conversationHistory);

        if ($modelType === 'image') {
            return $this->handleImageGeneration($parameters);
        } else {
            return $this->handleTextGeneration($parameters, $conversationHistory);
        }
    }

    protected function detectModelType($parameters, $conversationHistory)
    {
        $detectionPrompt = [
            [
                'role' => 'system',
                'content' => 'Analyze the user request and respond with exactly one word: "image" if the request involves generating, creating, drawing, or visualizing something graphical/diagrammatic, or "text" for all other requests. Examples: "Draw a diagram"="image", "Explain photosynthesis"="text", "Create a chart"="image", "What is mathematics"="text"'
            ],
            [
                'role' => 'user',
                'content' => "User request: " . ($parameters['input'] ?? '') .
                    "\n\nContext: " . json_encode(array_slice($conversationHistory, -3))
            ]
        ];

        try {
            $response = $this->chatGPTService->chat($detectionPrompt, 'gpt-4.1-nano');

            // Extract text from various response formats
            $responseText = $this->extractTextFromResponse($response);
            $result = trim(strtolower($responseText));

            \Log::info('Model detection result', ['raw_response' => $response, 'parsed_result' => $result]);
            return $result === 'image' ? 'image' : 'text';
        } catch (Exception $e) {
            \Log::error('Model detection failed', ['error' => $e->getMessage()]);
            return 'text'; // Default to text
        }
    }

    /**
     * Generate educational chat response with context parameters
     */
    public function chat(array $parameters, array $messages = []): array
    {
        $user = auth()->user();

        Log::info('Academic Chat Request Started', [
            'user_id' => $user?->id,
            'has_input' => isset($parameters['input']),
            'input_preview' => isset($parameters['input']) ? substr($parameters['input'], 0, 300) : 'none',
            'request_type' => $parameters['request_type'] ?? 'general',
        ]);

        // Check if user has active subscription and sufficient tokens
        if ($user && !$user->hasOpenAiTokens()) {
            Log::warning('Insufficient tokens', ['user_id' => $user->id]);

            return [
                'success' => false,
                'error' => 'Insufficient tokens. Please purchase a token package to continue.'
            ];
        }

        // Get the appropriate model for this user
        $modelToUse = $user ? $user->getOpenAiModel() : $this->model;

        // Prepare messages array
        $formattedMessages = [];

        // For quiz generation, DON'T use buildEducationalSystemMessage
        // The prompt already contains all necessary instructions
        if (isset($parameters['request_type']) && $parameters['request_type'] === 'quiz_generation') {
            // Use ONLY the provided prompt - don't add educational context
            if (isset($parameters['input'])) {
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $parameters['input']
                ];
            }
        } else {
            // For regular chat, use educational system message
            $systemMessage = $this->buildEducationalSystemMessage($parameters);
            $formattedMessages[] = ['role' => 'system', 'content' => $systemMessage];

            // Add conversation history if provided
            if (!empty($messages)) {
                $formattedMessages = array_merge($formattedMessages, $messages);
            }

            // Add current user message if provided in parameters
            if (isset($parameters['input'])) {
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $parameters['input']
                ];
            }
        }

        $requestData = [
            'model' => $modelToUse,
            'input' => $formattedMessages,
            'temperature' => (float) ($parameters['creativity_level'] ?? 1.0),
        ];

        $tokenLimit = (int) ($parameters['response_length'] ?? 10000);
        $requestData['max_output_tokens'] = $tokenLimit;

        if (isset($parameters['top_p'])) {
            $requestData['top_p'] = $parameters['top_p'];
        }

        // Rest of the method stays the same...
        $timeout = config('openai.openai.timeout', 90);
        $maxRetries = 3;
        $retryDelay = 2;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($timeout)
                    ->connectTimeout(10)
                    ->retry(2, 1000)
                    ->post($this->endpoint, $requestData);

                if ($response->successful()) {
                    $responseData = $response->json();

                    Log::info('OpenAI Response received', [
                        'has_output' => isset($responseData['output']),
                        'has_usage' => isset($responseData['usage']),
                    ]);

                    $usage = $responseData['usage'] ?? null;

                    if ($user && $usage) {
                        $this->logTokenUsage($user, $usage, $parameters['request_type'] ?? 'chat', $modelToUse);
                    }

                    $content = $this->extractContentFromResponsesAPI($responseData);

                    Log::warning('OpenAI Response Content', [
                       // 'content' => $content
                    ]);

                    return [
                        'success' => true,
                        'content' => $content,
                        'usage' => $usage,
                        'model' => $responseData['model'] ?? $modelToUse
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

                return [
                    'success' => false,
                    'error' => "API Error: " . $response->body()
                ];

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
                    'error' => 'Connection timeout. Please try again.'
                ];

            } catch (Exception $e) {
                Log::error('Educational Chat Service Error', [
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
                    'error' => 'Service temporarily unavailable. Please try again.'
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Service temporarily unavailable after multiple attempts. Please try again later.'
        ];
    }

    protected function extractContentFromResponsesAPI(array $responseData): string
    {
        if (!isset($responseData['output'])) {
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

                if (!empty($fullText)) {
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
            'output_structure' => json_encode($output)
        ]);
        return $this->extractTextFromResponse($responseData);
    }
    /**
     * Build educational system message based on parameters
     */
    protected function buildEducationalSystemMessage(array $parameters): string
    {
        $systemPrompt = "You are an AI educational assistant designed to help students learn effectively. ";

        // Age-appropriate communication
        if (isset($parameters['age'])) {
            $age = $parameters['age'];
            if ($age < 12) {
                $systemPrompt .= "Use simple, friendly language appropriate for elementary students. ";
            } elseif ($age < 16) {
                $systemPrompt .= "Use clear, engaging language appropriate for middle school students. ";
            } elseif ($age < 18) {
                $systemPrompt .= "Use comprehensive language appropriate for high school students. ";
            } else {
                $systemPrompt .= "Use academic language appropriate for college-level students. ";
            }
        }

        // Academic level
        if (isset($parameters['academic_level'])) {
            $level = strtolower($parameters['academic_level']);
            $systemPrompt .= "Tailor your responses for {$level} level understanding. ";
        }

        // Academic group/curriculum
        if (isset($parameters['academic_group'])) {
            $systemPrompt .= "Focus on the {$parameters['academic_group']} curriculum standards. ";
        }

        // Subject focus
        if (isset($parameters['subject'])) {
            $systemPrompt .= "You are specifically helping with {$parameters['subject']}. ";
        }

        // Topics and subtopics
        if (isset($parameters['topics']) && is_array($parameters['topics'])) {
            $topics = implode(', ', $parameters['topics']);
            $systemPrompt .= "Focus on these topics: {$topics}. ";
        }

        if (isset($parameters['subtopics']) && is_array($parameters['subtopics'])) {
            $subtopics = implode(', ', $parameters['subtopics']);
            $systemPrompt .= "Pay special attention to these subtopics: {$subtopics}. ";
        }

        // Learning style
        if (isset($parameters['learning_style'])) {
            $style = $parameters['learning_style'];
            switch ($style) {
                case 'visual':
                    $systemPrompt .= "Use visual descriptions, diagrams concepts, and examples that can be easily visualized. ";
                    break;
                case 'auditory':
                    $systemPrompt .= "Explain concepts through verbal descriptions, analogies, and step-by-step explanations. ";
                    break;
                case 'kinesthetic':
                    $systemPrompt .= "Suggest hands-on activities, practical applications, and real-world examples. ";
                    break;
                case 'reading':
                    $systemPrompt .= "Provide detailed written explanations with structured information and references. ";
                    break;
            }
        }

        // Difficulty preference
        if (isset($parameters['difficulty'])) {
            $difficulty = $parameters['difficulty'];
            $systemPrompt .= "Adjust the complexity to {$difficulty} level. ";
        }

        // Special accommodations
        if (isset($parameters['accommodations']) && is_array($parameters['accommodations'])) {
            $accommodations = $parameters['accommodations'];
            if (in_array('simplified_language', $accommodations)) {
                $systemPrompt .= "Use simplified vocabulary and shorter sentences. ";
            }
            if (in_array('step_by_step', $accommodations)) {
                $systemPrompt .= "Break down complex concepts into clear, numbered steps. ";
            }
            if (in_array('examples_heavy', $accommodations)) {
                $systemPrompt .= "Provide multiple examples for each concept explained. ";
            }
        }

        // Response format preference
        if (isset($parameters['response_format'])) {
            $format = $parameters['response_format'];
            switch ($format) {
                case 'detailed':
                    $systemPrompt .= "Provide comprehensive, detailed explanations. ";
                    break;
                case 'concise':
                    $systemPrompt .= "Keep responses concise and to the point. ";
                    break;
                case 'interactive':
                    $systemPrompt .= "Make responses interactive by asking follow-up questions. ";
                    break;
            }
        }

        $systemPrompt .= "\n\nGeneral guidelines:\n";
        $systemPrompt .= "- Always be encouraging and supportive\n";
        $systemPrompt .= "- Check for understanding before moving to complex topics\n";
        $systemPrompt .= "- Use examples relevant to the student's age and interests\n";
        $systemPrompt .= "- If asked about inappropriate content, redirect to educational topics\n";
        $systemPrompt .= "- Encourage critical thinking and curiosity\n";
        $systemPrompt .= "- Provide sources or suggest further reading when appropriate";
        $systemPrompt .= "- No greetings is required or acknowledgement of a valid question";
        $systemPrompt .= "- Avoid phrases such as 'That's a great question!' or similar";

        return $systemPrompt;
    }

    /**
     * Log token usage and deduct from user's subscription
     */
    protected function logTokenUsage($user, array $usage, string $requestType = 'chat', string $model = null): void
    {
        $subscription = $user->activeTokenSubscription;

        if (!$subscription) {
            return;
        }

        $totalTokens = $usage['total_tokens'] ?? 0;

        // Create usage log
        OpenAiTokenUsageLog::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'model' => $model ?? $this->model,
            'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
            'completion_tokens' => $usage['max_output_tokens'] ?? 0,
            'total_tokens' => $totalTokens,
            'request_type' => $requestType,
        ]);

        // Deduct tokens from subscription
        $subscription->deductTokens($totalTokens);

        // Dispatch event for real-time updates
        event(new TokenUsageUpdated($user->id));
    }

    private function extractTextFromResponse($response)
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

        return (string)$response;
    }

protected function handleImageGeneration($parameters)
{
    // Extract image generation details from parameters
    $prompt = $this->prepareImagePrompt($parameters);

    try {
        // Get the appropriate image model for the current user
        $user = auth()->user();
        $model = $this->modelSelectionService->getImageModelForUser($user);

        $images = $this->chatGPTService->generateImage($prompt, $model);
        dd($images);
        $this->logTokenUsage($user, $images[0]['usage'], 'image', $model);


        // Create markdown content with images
        $responseContent = "Here is the generated image based on your request:\n\n";

        // Also prepare images for direct access
        $imageData = [];
        foreach ($images as $image) {
            $responseContent .= "![Generated Image]({$image['url']})\n\n";
            $imageData[] = [
                'url' => $image['url'],
                'revised_prompt' => $image['revised_prompt'] ?? null
            ];
        }

        return [
            'success' => true,
            'content' => $responseContent,
            'model_used' => $model,
            'images' => $imageData
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

    protected function prepareImagePrompt($parameters)
    {
        // Create a detailed prompt for image generation
        $description = $parameters['input'] ?? 'Generate an educational image';

        $details = [];
        if (!empty($parameters['subject'])) {
            $details[] = "Subject: " . $parameters['subject'];
        }

        if (!empty($parameters['topics'])) {
            $details[] = "Topics: " . (is_array($parameters['topics']) ? implode(', ', $parameters['topics']) : $parameters['topics']);
        }

        if (!empty($parameters['academic_level'])) {
            $details[] = "Academic level: " . $parameters['academic_level'];
        }

        $prompt = $description;
        if (!empty($details)) {
            $prompt .= ". " . implode('. ', $details);
        }

        // Add style guidance for educational content
        $prompt .= ". Educational, clear, professional style, suitable for academic purposes";

        return $prompt;
    }

    protected function handleTextGeneration($parameters, $conversationHistory)
    {
        // Prepare the prompt for text generation
        $prompt = $this->prepareTextPrompt($parameters);

        // Add to conversation history
        $messages = array_merge($conversationHistory, [['role' => 'user', 'content' => $prompt]]);

        try {
            // Get the appropriate model for the current user
            $user = auth()->user();
            $model = $this->modelSelectionService->getTextModelForUser($user);

            $content = $this->chatGPTService->chat($messages, $model);
            $normalizedContent = $this->normalizeTextResponse($content);

            return [
                'success' => true,
                'content' => $normalizedContent['text'],
                'raw_content' => $normalizedContent['raw'],
                'model_used' => $model
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

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

    protected function prepareTextPrompt($parameters)
    {
        $prompt = "User request: " . ($parameters['input'] ?? '');

        // Add context from parameters
        $context = [];
        foreach ($parameters as $key => $value) {
            if ($key !== 'input' && !empty($value)) {
                if (is_array($value)) {
                    $context[] = ucfirst(str_replace('_', ' ', $key)) . ": " . implode(', ', $value);
                } else {
                    $context[] = ucfirst(str_replace('_', ' ', $key)) . ": " . $value;
                }
            }
        }

        if (!empty($context)) {
            $prompt .= "\n\nContext:\n" . implode("\n", $context);
        }

        return $prompt;
    }

    /**
     * Validate educational parameters
     */
    public function validateParameters(array $parameters): array
    {
        $errors = [];

        // Required parameters
        if (!isset($parameters['input']) || empty(trim($parameters['input']))) {
            $errors[] = 'Message is required';
        }

        // Age validation
        if (isset($parameters['age']) && (!is_numeric($parameters['age']) || $parameters['age'] < 5 || $parameters['age'] > 100)) {
            $errors[] = 'Age must be between 5 and 100';
        }

        // Academic level validation
        $validLevels = ['elementary', 'middle_school', 'high_school', 'college', 'graduate'];
        if (isset($parameters['academic_level']) && !in_array($parameters['academic_level'], $validLevels)) {
            $errors[] = 'Invalid academic level';
        }

        // Learning style validation
        $validStyles = ['visual', 'auditory', 'kinesthetic', 'reading'];
        if (isset($parameters['learning_style']) && !in_array($parameters['learning_style'], $validStyles)) {
            $errors[] = 'Invalid learning style';
        }

        // Creativity level validation
        if (((isset($parameters['creativity_level']) && (!is_numeric($parameters['creativity_level']))) || $parameters['creativity_level'] < 1)) {
            $errors[] = 'Creativity level must be greater than 1';
        }

        return $errors;
    }

    /**
     * Get available subjects
     */
    public function getAvailableSubjects(): array
    {
        return [
            'mathematics' => ['algebra', 'geometry', 'calculus', 'statistics', 'trigonometry'],
            'science' => ['physics', 'chemistry', 'biology', 'earth_science', 'astronomy'],
            'language_arts' => ['grammar', 'literature', 'writing', 'reading_comprehension', 'poetry'],
            'social_studies' => ['history', 'geography', 'civics', 'economics', 'psychology'],
            'computer_science' => ['programming', 'algorithms', 'data_structures', 'web_development', 'databases'],
            'foreign_languages' => ['spanish', 'french', 'german', 'mandarin', 'japanese'],
            'arts' => ['visual_arts', 'music', 'theater', 'dance', 'digital_arts']
        ];
    }

    /**
     * Extract text content from an uploaded file
     */
    public function extractFileContentDeprecated(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();

        try {
            // Handle different file types
            if (str_starts_with($mimeType, 'text/')) {
                // Plain text files
                $content = $file->getContent();
                if (empty(trim($content))) {
                    return "The uploaded text file appears to be empty.";
                }
                return $content;
            } elseif ($mimeType === 'application/pdf') {
                // PDF files
                $content = $this->extractPdfContent($file);
                if (str_contains($content, 'requires pdftotext')) {
                    throw new Exception("PDF content extraction failed. Please ensure pdftotext is installed on the server.");
                }
                return $content;
            } elseif (in_array($mimeType, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ])) {
                // Word documents
                $content = $this->extractWordContent($file);
                if (str_contains($content, 'requires the phpoffice/phpword library')) {
                    throw new Exception("Word document content extraction failed. Please install the phpoffice/phpword library.");
                }
                return $content;
            } elseif (in_array($mimeType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])) {
                // Excel documents
                $content = $this->extractExcelContent($file);
                if (str_contains($content, 'requires the phpoffice/phpspreadsheet library')) {
                    throw new Exception("Excel document content extraction failed. Please install the phpoffice/phpspreadsheet library.");
                }
                return $content;
            } else {
                // For other file types, try to get basic content
                $content = $file->getContent();
                if (!empty($content) && strlen($content) < 10000) { // Reasonable limit
                    return $content;
                }

                // If all else fails, return file info
                return "File uploaded:\n" .
                    "- Name: " . $originalName . "\n" .
                    "- Size: " . $file->getSize() . " bytes\n" .
                    "- Type: " . $mimeType . "\n" .
                    "Note: Content could not be automatically extracted from this file type.";
            }
        } catch (Exception $e) {
            \Log::error('File extraction error', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'mime_type' => $mimeType
            ]);

            // Return a more informative error message
            return "Error processing file: " . $originalName . "\n" .
                "Issue: " . $e->getMessage() . "\n" .
                "Please try uploading a plain text file (.txt) or ensure the required extraction tools are installed.";
        }
    }

    /**
     * Extract content from PDF files
     */
    protected function extractPdfContent(UploadedFile $file): string
    {
        try {
            $pdfExtractor = app(PdfContentExtractionService::class);
            return $pdfExtractor->extractFromUploadedFile($file, [
                'preserve_layout' => true,
                'method' => 'auto'
            ]);
        } catch (Exception $e) {
            Log::error("PDF extraction failed: {$e->getMessage()}");

            return "PDF file content extraction failed.\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n" .
                "Error: " . $e->getMessage();
        }
    }

    /**
     * Extract content from Word documents
     */
    protected function extractWordContent(UploadedFile $file): string
    {
        // For .doc files
        if ($file->getMimeType() === 'application/msword') {
            return "Word document (.doc) uploaded.\n" .
                "Full content extraction for .doc files requires the phpoffice/phpword library.\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n";
        }

        // For .docx files
        if ($file->getMimeType() === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $tmpFile = $file->getRealPath();

            // Try to extract basic text content from DOCX (XML-based format)
            $zip = new ZipArchive();
            if ($zip->open($tmpFile) === TRUE) {
                $content = '';
                // Read document.xml which contains the main text
                if ($zip->locateName('word/document.xml') !== false) {
                    $xml = $zip->getFromName('word/document.xml');
                    $dom = new DOMDocument();
                    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $content = strip_tags($dom->textContent);
                }
                $zip->close();

                if (!empty($content)) {
                    return trim($content);
                }
            }

            // Fallback if XML parsing fails
            return "Word document (.docx) uploaded.\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n";
        }

        return "Unsupported Word document format.\n";
    }

    /**
     * Extract content from Excel documents
     */
    protected function extractExcelContent(UploadedFile $file): string
    {
        // For .xls files
        if ($file->getMimeType() === 'application/vnd.ms-excel') {
            return "Excel document (.xls) uploaded.\n" .
                "Full content extraction for .xls files requires the phpoffice/phpspreadsheet library.\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n";
        }

        // For .xlsx files
        if ($file->getMimeType() === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $tmpFile = $file->getRealPath();

            // Try to extract basic information from XLSX (XML-based format)
            $zip = new ZipArchive();
            if ($zip->open($tmpFile) === TRUE) {
                $content = '';
                // Read sharedStrings.xml which contains cell values
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $xml = $zip->getFromName('xl/sharedStrings.xml');
                    $dom = new DOMDocument();
                    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $content = strip_tags($dom->textContent);
                }
                $zip->close();

                if (!empty($content)) {
                    return "Excel spreadsheet content (text values only):\n" . trim($content);
                }
            }

            // Fallback if XML parsing fails
            return "Excel document (.xlsx) uploaded.\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n";
        }

        return "Unsupported Excel document format.\n";
    }

    /**
     * Extract content from various file types
     *
     * @param UploadedFile $file
     * @return string
     */
    public function extractFileContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractPdfContent($file),
            'txt' => file_get_contents($file->getRealPath()),
            'doc', 'docx' => $this->extractDocxContent($file),
            default => "Unsupported file type: {$extension}\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n"
        };
    }

    /**
     * Extract content from DOCX files
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function extractDocxContent(UploadedFile $file): string
    {
        try {
            if (!class_exists(IOFactory::class)) {
                throw new RuntimeException('PhpWord library not available');
            }

            $tmpPath = $file->getRealPath();
            $phpWord = IOFactory::load($tmpPath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            return trim($text);
        } catch (Exception $e) {
            Log::error('DOCX processing failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            return "Error processing document: " . $e->getMessage() . "\n" .
                "File name: " . $file->getClientOriginalName() . "\n" .
                "File size: " . $file->getSize() . " bytes\n";
        }
    }

    protected function isCommandAvailable(string $command): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $output = [];
        $returnCode = 0;
        @exec("which {$command} 2>&1", $output, $returnCode);

        return $returnCode === 0;
    }

}
