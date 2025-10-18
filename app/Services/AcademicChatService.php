<?php

namespace App\Services;

use App\Events\TokenUsageUpdated;
use App\Models\Chat\OpenAiTokenUsageLog;
use DOMDocument;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class AcademicChatService
{
    protected mixed $apiKey;
    protected string $endpoint = 'https://api.openai.com/v1/chat/completions';
    protected string $model = '';

    public function __construct($model = null)
    {
        $this->apiKey = config('openai.openai.api_key');
        $this->model = $model ?? config('openai.openai.default_model');
    }


    /**
     * Generate educational chat response with context parameters
     */

    public function chat(array $parameters, array $messages = []): array
    {

        $user = auth()->user();

        Log::info('Academic Chat Request Started', [
            'user_id' => $user?->id,
            'has_message' => isset($parameters['message']),
            'message_length' => isset($parameters['message']) ? strlen($parameters['message']) : 0,
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


        // Build system message based on educational parameters
        $systemMessage = $this->buildEducationalSystemMessage($parameters);

        // Prepare messages array
        $formattedMessages = [
            ['role' => 'system', 'content' => $systemMessage]
        ];

        // Add conversation history if provided
        if (!empty($messages)) {
            $formattedMessages = array_merge($formattedMessages, $messages);
        }

        // Add current user message if provided in parameters
        if (isset($parameters['message'])) {
            $formattedMessages[] = [
                'role' => 'user',
                'content' => $parameters['message']
            ];
        }

        $requestData = [
            'model' => $modelToUse,
            'messages' => $formattedMessages,
            'temperature' => (float)$parameters['creativity_level'] ?? 1,
        ];

        $tokenLimit = (int)$parameters['response_length'] ?? 1000;

        $requestData['max_completion_tokens'] = $tokenLimit;


        // Add additional OpenAI parameters if specified
        if (isset($parameters['top_p'])) {
            $requestData['top_p'] = $parameters['top_p'];
        }

        Log::info('Sending request to OpenAI', [
            'model' => $modelToUse,
            'message_count' => count($formattedMessages),
            'has_temperature' => isset($requestData['temperature']),
            'token_limit' => $tokenLimit,
        ]);

        // Get timeout from config or use 90 seconds as default (increased from 30)
        $timeout = config('openai.openai.timeout', 90);
        $maxRetries = 3;
        $retryDelay = 2; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('OpenAI API attempt', ['attempt' => $attempt, 'max_retries' => $maxRetries]);

                $response = Http::withToken($this->apiKey)
                    ->timeout($timeout)
                    ->connectTimeout(10) // Add connection timeout
                    ->retry(2, 1000) // Retry 2 times with 1 second delay for connection issues
                    ->post($this->endpoint, $requestData);

                Log::info('OpenAI Response Received', [
                    'status' => $response->status(),
                    'successful' => $response->successful(),
                    'attempt' => $attempt,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $usage = $responseData['usage'] ?? null;

                    Log::info('OpenAI Response Success', [
                        'has_usage' => $usage !== null,
                        'data' => $responseData,
                        'model_used' => $responseData['model'] ?? 'unknown',
                    ]);

                    // Log token usage and deduct from user's subscription
                    if ($user && $usage) {
                        $this->logTokenUsage($user, $usage, $parameters['request_type'] ?? 'chat');
                    }

                    return [
                        'success' => true,
                        'content' => $responseData['choices'][0]['message']['content'],
                        'usage' => $usage,
                        'model' => $responseData['model'] ?? $modelToUse
                    ];
                }

                // Handle 429 (rate limit) and 503 (service unavailable) with retry
                if (in_array($response->status(), [429, 503, 502])) {
                    if ($attempt < $maxRetries) {
                        $waitTime = $retryDelay * $attempt; // Exponential backoff
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

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Handle connection/timeout errors specifically
                Log::error('OpenAI Connection Error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries,
                ]);

                if ($attempt < $maxRetries) {
                    $waitTime = $retryDelay * $attempt;
                    Log::info('Retrying after connection error', ['wait_time' => $waitTime]);
                    sleep($waitTime);
                    continue;
                }

                return [
                    'success' => false,
                    'error' => 'Connection timeout. The AI service is taking longer than expected. Please try again with a shorter message or fewer conversation history.'
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

        // If all retries failed
        return [
            'success' => false,
            'error' => 'Service temporarily unavailable after multiple attempts. Please try again later.'
        ];
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
            'completion_tokens' => $usage['max_completion_tokens'] ?? 0,
            'total_tokens' => $totalTokens,
            'request_type' => $requestType,
        ]);

        // Deduct tokens from subscription
        $subscription->deductTokens($totalTokens);

        // Dispatch event for real-time updates
        event(new TokenUsageUpdated($user->id));
    }

    /**
     * Validate educational parameters
     */
    public function validateParameters(array $parameters): array
    {
        $errors = [];

        // Required parameters
        if (!isset($parameters['message']) || empty(trim($parameters['message']))) {
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
        if ((isset($parameters['creativity_level']) && (!is_numeric($parameters['creativity_level'])) || $parameters['creativity_level'] < 1 )) {
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
    public function extractFileContent(UploadedFile $file): string
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
        // Check if pdftotext is available
        if (function_exists('exec') && $this->isCommandAvailable('pdftotext')) {
            $tmpFile = $file->getRealPath();
            $outputFile = tempnam(sys_get_temp_dir(), 'pdf_output');

            // Use pdftotext command line tool
            $command = "pdftotext -layout " . escapeshellarg($tmpFile) . " " . escapeshellarg($outputFile);
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFile)) {
                $content = file_get_contents($outputFile);
                unlink($outputFile);
                return trim($content);
            }

            // Fallback if pdftotext fails
            unlink($outputFile);
        }

        // If pdftotext is not available or fails, try to use the simple approach
        return "PDF file content extraction requires pdftotext to be installed on the server.\n" .
            "File name: " . $file->getClientOriginalName() . "\n" .
            "File size: " . $file->getSize() . " bytes\n";
    }

    /**
     * Check if a command is available on the system
     */
    protected function isCommandAvailable(string $command): bool
    {
        $returnCode = null;
        $output = [];
        exec("which $command", $output, $returnCode);
        return $returnCode === 0;
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

}
