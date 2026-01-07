<?php

namespace App\Services;

use App\Services\Traits\ResponseExtraction;
use DOMDocument;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use ZipArchive;

class AcademicChatService
{
    use ResponseExtraction;

    protected string $model = '';

    protected ChatGPTService $chatGPTService;

    protected ModelSelectionService $modelSelectionService;

    protected TokenUsageService $tokenUsageService;

    public function __construct(
        ChatGPTService $chatGPTService,
        ModelSelectionService $modelSelectionService,
        TokenUsageService $tokenUsageService
    ) {
        $this->chatGPTService = $chatGPTService;
        $this->modelSelectionService = $modelSelectionService;
        $this->tokenUsageService = $tokenUsageService;
    }

    public function processRequest($parameters, $conversationHistory)
    {
        $user = auth()->user();

        if ($user && ! $user->hasOpenAiTokens()) {
            return ['success' => false, 'error' => 'Insufficient tokens.'];
        }

        // 1. Fast Regex Check for image intent to save classification tokens
        $input = $parameters['input'] ?? '';
        $modelType = 'text';

        if (preg_match('/(draw|generate|create|visualize|image|picture|diagram|chart|graph)/i', $input)) {
            // 2. Delegate to ModelSelectionService for AI-based classification
            $modelType = $this->modelSelectionService->detectModelType(
                $this->chatGPTService,
                $parameters,
                $conversationHistory
            );
        }

        if ($modelType === 'image') {
            return $this->handleImageGeneration($parameters);
        }

        return $this->handleTextGeneration($parameters, $conversationHistory);
    }

    protected function handleImageGeneration($parameters): array
    {
        $user = auth()->user();
        $model = $this->modelSelectionService->getImageModelForUser($user);
        $prompt = $this->prepareImagePrompt($parameters);

        $result = $this->chatGPTService->generateImage($prompt, $model);

        if (! $result['success']) {
            return $result;
        }

        $responseContent = "Here is the generated image:\n\n";
        $imageData = [];
        foreach ($result['images'] as $image) {
            $responseContent .= "![Generated Image]({$image['url']})\n\n";
            $imageData[] = ['url' => $image['url'], 'revised_prompt' => $image['revised_prompt'] ?? null];
        }

        return [
            'success' => true,
            'content' => $responseContent,
            'model_used' => $model,
            'images' => $imageData,
        ];
    }

    protected function prepareImagePrompt($parameters): string
    {
        // Create a detailed prompt for image generation
        $description = $parameters['input'] ?? 'Generate an educational image';

        $details = [];
        if (! empty($parameters['subject'])) {
            $details[] = 'Subject: '.$parameters['subject'];
        }

        if (! empty($parameters['topics'])) {
            $details[] = 'Topics: '.(is_array($parameters['topics']) ? implode(', ', $parameters['topics']) : $parameters['topics']);
        }

        if (! empty($parameters['academic_level'])) {
            $details[] = 'Academic level: '.$parameters['academic_level'];
        }

        $prompt = $description;
        if (! empty($details)) {
            $prompt .= '. '.implode('. ', $details);
        }

        // Add style guidance for educational content
        $prompt .= '. Educational, clear, professional style, suitable for academic purposes';

        return $prompt;
    }

    protected function handleTextGeneration($parameters, $conversationHistory): array
    {
        $user = auth()->user();
        $model = $this->modelSelectionService->getTextModelForUser($user);

        // Build messages using existing educational prompt logic
        $messages = [];
        if (($parameters['request_type'] ?? '') !== 'quiz_generation') {
            $messages[] = ['role' => 'system', 'content' => $this->buildEducationalSystemMessage($parameters)];
        }

        $messages = array_merge($messages, $conversationHistory);
        $messages[] = ['role' => 'user', 'content' => $this->prepareTextPrompt($parameters)];

        return $this->chatGPTService->chat($messages, $model, [
            'temperature' => (float) ($parameters['creativity_level'] ?? 1.0),
            'max_output_tokens' => (int) ($parameters['response_length'] ?? 10000),
            //            'request_type' => $parameters['request_type'] ?? 'chat'
        ]);
    }

    /**
     * Build educational system message based on parameters
     */
    protected function buildEducationalSystemMessage(array $parameters): string
    {
        $systemPrompt = 'You are an AI educational assistant designed to help students learn effectively. ';

        // Age-appropriate communication
        if (isset($parameters['age'])) {
            $age = $parameters['age'];
            if ($age < 12) {
                $systemPrompt .= 'Use simple, friendly language appropriate for elementary students. ';
            } elseif ($age < 16) {
                $systemPrompt .= 'Use clear, engaging language appropriate for middle school students. ';
            } elseif ($age < 18) {
                $systemPrompt .= 'Use comprehensive language appropriate for high school students. ';
            } else {
                $systemPrompt .= 'Use academic language appropriate for college-level students. ';
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
                    $systemPrompt .= 'Use visual descriptions, diagrams concepts, and examples that can be easily visualized. ';
                    break;
                case 'auditory':
                    $systemPrompt .= 'Explain concepts through verbal descriptions, analogies, and step-by-step explanations. ';
                    break;
                case 'kinesthetic':
                    $systemPrompt .= 'Suggest hands-on activities, practical applications, and real-world examples. ';
                    break;
                case 'reading':
                    $systemPrompt .= 'Provide detailed written explanations with structured information and references. ';
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
                $systemPrompt .= 'Use simplified vocabulary and shorter sentences. ';
            }
            if (in_array('step_by_step', $accommodations)) {
                $systemPrompt .= 'Break down complex concepts into clear, numbered steps. ';
            }
            if (in_array('examples_heavy', $accommodations)) {
                $systemPrompt .= 'Provide multiple examples for each concept explained. ';
            }
        }

        // Response format preference
        if (isset($parameters['response_format'])) {
            $format = $parameters['response_format'];
            switch ($format) {
                case 'detailed':
                    $systemPrompt .= 'Provide comprehensive, detailed explanations. ';
                    break;
                case 'concise':
                    $systemPrompt .= 'Keep responses concise and to the point. ';
                    break;
                case 'interactive':
                    $systemPrompt .= 'Make responses interactive by asking follow-up questions. ';
                    break;
            }
        }

        $systemPrompt .= "\n\nGeneral guidelines:\n";
        $systemPrompt .= "- Always be encouraging and supportive\n";
        $systemPrompt .= "- Check for understanding before moving to complex topics\n";
        $systemPrompt .= "- Use examples relevant to the student's age and interests\n";
        $systemPrompt .= "- If asked about inappropriate content, redirect to educational topics\n";
        $systemPrompt .= "- Encourage critical thinking and curiosity\n";
        $systemPrompt .= '- Provide sources or suggest further reading when appropriate';
        $systemPrompt .= '- No greetings is required or acknowledgement of a valid question';
        $systemPrompt .= "- Avoid phrases such as 'That's a great question!' or similar";

        return $systemPrompt;
    }

    protected function prepareTextPrompt($parameters): string
    {
        $prompt = 'User request: '.($parameters['input'] ?? '');

        // Add context from parameters
        $context = [];
        foreach ($parameters as $key => $value) {
            if ($key !== 'input' && ! empty($value)) {
                if (is_array($value)) {
                    $context[] = ucfirst(str_replace('_', ' ', $key)).': '.implode(', ', $value);
                } else {
                    $context[] = ucfirst(str_replace('_', ' ', $key)).': '.$value;
                }
            }
        }

        if (! empty($context)) {
            $prompt .= "\n\nContext:\n".implode("\n", $context);
        }

        return $prompt;
    }

    /**
     * Generate educational chat response with context parameters
     * Prepares educational messages and delegates to ChatGPTService's centralized chat method
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
        if ($user && ! $user->hasOpenAiTokens()) {
            Log::warning('Insufficient tokens', ['user_id' => $user->id]);

            return [
                'success' => false,
                'error' => 'Insufficient tokens. Please purchase a token package to continue.',
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
                    'content' => $parameters['input'],
                ];
            }
        } else {
            // For regular chat, use educational system message
            $systemMessage = $this->buildEducationalSystemMessage($parameters);
            $formattedMessages[] = ['role' => 'system', 'content' => $systemMessage];

            // Add conversation history if provided
            if (! empty($messages)) {
                $formattedMessages = array_merge($formattedMessages, $messages);
            }

            // Add current user message if provided in parameters
            if (isset($parameters['input'])) {
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $parameters['input'],
                ];
            }
        }

        // Build request data with educational parameters
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

        // Delegate to centralized chat request handler with retry logic
        $requestOptions = [
            'request_type' => $parameters['request_type'] ?? 'chat',
        ];

        $result = $this->chatGPTService->chat($formattedMessages, $modelToUse, array_merge($requestOptions, [
            'temperature' => $requestData['temperature'],
            'max_output_tokens' => $requestData['max_output_tokens'],
            'top_p' => $requestData['top_p'] ?? null,
        ]));

        if ($result['success']) {
            Log::info('OpenAI Response received', [
                'has_content' => ! empty($result['content']),
                'has_usage' => isset($result['usage']),
            ]);
        }

        return $result;
    }

    /**
     * Validate educational parameters
     */
    public function validateParameters(array $parameters): array
    {
        $errors = [];

        // Required parameters
        if (! isset($parameters['input']) || empty(trim($parameters['input']))) {
            $errors[] = 'Message is required';
        }

        // Age validation
        if (isset($parameters['age']) && (! is_numeric($parameters['age']) || $parameters['age'] < 5 || $parameters['age'] > 100)) {
            $errors[] = 'Age must be between 5 and 100';
        }

        // Academic level validation
        $validLevels = ['elementary', 'middle_school', 'high_school', 'college', 'graduate'];
        if (isset($parameters['academic_level']) && ! in_array($parameters['academic_level'], $validLevels)) {
            $errors[] = 'Invalid academic level';
        }

        // Learning style validation
        $validStyles = ['visual', 'auditory', 'kinesthetic', 'reading'];
        if (isset($parameters['learning_style']) && ! in_array($parameters['learning_style'], $validStyles)) {
            $errors[] = 'Invalid learning style';
        }

        // Creativity level validation
        if (((isset($parameters['creativity_level']) && (! is_numeric($parameters['creativity_level']))) || $parameters['creativity_level'] < 1)) {
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
            'arts' => ['visual_arts', 'music', 'theater', 'dance', 'digital_arts'],
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
                    return 'The uploaded text file appears to be empty.';
                }

                return $content;
            } elseif ($mimeType === 'application/pdf') {
                // PDF files
                $content = $this->extractPdfContent($file);
                if (str_contains($content, 'requires pdftotext')) {
                    throw new Exception('PDF content extraction failed. Please ensure pdftotext is installed on the server.');
                }

                return $content;
            } elseif (in_array($mimeType, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])) {
                // Word documents
                $content = $this->extractWordContent($file);
                if (str_contains($content, 'requires the phpoffice/phpword library')) {
                    throw new Exception('Word document content extraction failed. Please install the phpoffice/phpword library.');
                }

                return $content;
            } elseif (in_array($mimeType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])) {
                // Excel documents
                $content = $this->extractExcelContent($file);
                if (str_contains($content, 'requires the phpoffice/phpspreadsheet library')) {
                    throw new Exception('Excel document content extraction failed. Please install the phpoffice/phpspreadsheet library.');
                }

                return $content;
            } else {
                // For other file types, try to get basic content
                $content = $file->getContent();
                if (! empty($content) && strlen($content) < 10000) { // Reasonable limit
                    return $content;
                }

                // If all else fails, return file info
                return "File uploaded:\n".
                    '- Name: '.$originalName."\n".
                    '- Size: '.$file->getSize()." bytes\n".
                    '- Type: '.$mimeType."\n".
                    'Note: Content could not be automatically extracted from this file type.';
            }
        } catch (Exception $e) {
            \Log::error('File extraction error', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'mime_type' => $mimeType,
            ]);

            // Return a more informative error message
            return 'Error processing file: '.$originalName."\n".
                'Issue: '.$e->getMessage()."\n".
                'Please try uploading a plain text file (.txt) or ensure the required extraction tools are installed.';
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
                'method' => 'auto',
            ]);
        } catch (Exception $e) {
            Log::error("PDF extraction failed: {$e->getMessage()}");

            return "PDF file content extraction failed.\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n".
                'Error: '.$e->getMessage();
        }
    }

    /**
     * Extract content from Word documents
     */
    protected function extractWordContent(UploadedFile $file): string
    {
        // For .doc files
        if ($file->getMimeType() === 'application/msword') {
            return "Word document (.doc) uploaded.\n".
                "Full content extraction for .doc files requires the phpoffice/phpword library.\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n";
        }

        // For .docx files
        if ($file->getMimeType() === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $tmpFile = $file->getRealPath();

            // Try to extract basic text content from DOCX (XML-based format)
            $zip = new ZipArchive;
            if ($zip->open($tmpFile) === true) {
                $content = '';
                // Read document.xml which contains the main text
                if ($zip->locateName('word/document.xml') !== false) {
                    $xml = $zip->getFromName('word/document.xml');
                    $dom = new DOMDocument;
                    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $content = strip_tags($dom->textContent);
                }
                $zip->close();

                if (! empty($content)) {
                    return trim($content);
                }
            }

            // Fallback if XML parsing fails
            return "Word document (.docx) uploaded.\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n";
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
            return "Excel document (.xls) uploaded.\n".
                "Full content extraction for .xls files requires the phpoffice/phpspreadsheet library.\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n";
        }

        // For .xlsx files
        if ($file->getMimeType() === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $tmpFile = $file->getRealPath();

            // Try to extract basic information from XLSX (XML-based format)
            $zip = new ZipArchive;
            if ($zip->open($tmpFile) === true) {
                $content = '';
                // Read sharedStrings.xml which contains cell values
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $xml = $zip->getFromName('xl/sharedStrings.xml');
                    $dom = new DOMDocument;
                    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $content = strip_tags($dom->textContent);
                }
                $zip->close();

                if (! empty($content)) {
                    return "Excel spreadsheet content (text values only):\n".trim($content);
                }
            }

            // Fallback if XML parsing fails
            return "Excel document (.xlsx) uploaded.\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n";
        }

        return "Unsupported Excel document format.\n";
    }

    /**
     * Extract content from various file types
     */
    public function extractFileContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractPdfContent($file),
            'txt' => file_get_contents($file->getRealPath()),
            'doc', 'docx' => $this->extractDocxContent($file),
            default => "Unsupported file type: {$extension}\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n"
        };
    }

    /**
     * Extract content from DOCX files
     */
    protected function extractDocxContent(UploadedFile $file): string
    {
        try {
            if (! class_exists(IOFactory::class)) {
                throw new RuntimeException('PhpWord library not available');
            }

            $tmpPath = $file->getRealPath();
            $phpWord = IOFactory::load($tmpPath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText()."\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText()."\n";
                            }
                        }
                    }
                }
            }

            return trim($text);
        } catch (Exception $e) {
            Log::error('DOCX processing failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return 'Error processing document: '.$e->getMessage()."\n".
                'File name: '.$file->getClientOriginalName()."\n".
                'File size: '.$file->getSize()." bytes\n";
        }
    }
}
