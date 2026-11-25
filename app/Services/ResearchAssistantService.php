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

class ResearchAssistantService
{
    use ResponseExtraction;

    protected string $model = 'gpt-6.2-sol';

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

        // 1. Fast Regex Check for image/mixed intent to save classification tokens
        $rawInput = $parameters['input'] ?? '';
        $input = is_string($rawInput) ? $rawInput : (is_array($rawInput) ? implode(' ', array_filter($rawInput, 'is_string')) : '');
        $modelType = 'text';

        if (preg_match('/(draw|create|generate|make)\s+(an?\s+)?(image|picture|photo|illustration|drawing|sketch|render)|illustration|diagram|chart|graph/i', $input)) {
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

        if ($modelType === 'mixed') {
            return $this->handleMixedGeneration($parameters, $conversationHistory);
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
            $url = $image['url'] ?? null;

            // If the image is base64-encoded, save it as a file to avoid storing large data in DB
            if ($url && str_starts_with($url, 'data:image')) {
                $url = $this->saveBase64Image($url);
            }

            $responseContent .= "![Generated Image]({$url})\n\n";
            $imageData[] = [
                'url' => $url,
                'revised_prompt' => $image['revised_prompt'] ?? null,
            ];
        }

        // Also scan the content for any inline image markdown links and extract them
        $imageData = $this->extractInlineImagesFromContent($responseContent, $imageData);

        return [
            'success' => true,
            'content' => $responseContent,
            'model_used' => $model,
            'images' => $imageData,
        ];
    }

    /**
     * Handle mixed content generation: text with illustrations.
     * If $preGeneratedContent is provided, uses it directly instead of generating text again.
     */
    public function handleMixedGeneration($parameters, $conversationHistory, string $preGeneratedContent = ''): array
    {
        // Step 1: Use pre-generated content if available, otherwise generate text
        if ($preGeneratedContent !== '') {
            Log::info('ResearchAssistantService: Using pre-generated content for mixed generation');
            $content = $preGeneratedContent;
            $images = [];
        } else {
            $textResult = $this->handleTextGeneration($parameters, $conversationHistory);

            if (! $textResult['success']) {
                return $textResult;
            }

            $content = $textResult['content'] ?? '';
            $images = $textResult['images'] ?? [];
        }

        // Step 2: Extract key concepts that need illustration
        $illustrationPrompts = $this->extractIllustrationPrompts($parameters, $content);

        if (empty($illustrationPrompts)) {
            Log::info('ResearchAssistantService: No illustration concepts found for mixed generation');
            return [
                'success' => true,
                'content' => $content,
                'images' => $images,
            ];
        }

        // Step 3: Generate images for each concept
        $user = auth()->user();
        $imageModel = $this->modelSelectionService->getImageModelForUser($user);

        foreach ($illustrationPrompts as $index => $concept) {
            try {
                $imageResult = $this->chatGPTService->generateImage($concept, $imageModel);

                if ($imageResult['success'] && ! empty($imageResult['images'])) {
                    foreach ($imageResult['images'] as $image) {
                        $url = $image['url'] ?? null;

                        // Normalize base64 data URIs
                        if ($url && str_starts_with($url, 'data:image')) {
                            $url = $this->saveBase64Image($url);
                        }

                        if ($url) {
                            $images[] = [
                                'url' => $url,
                                'alt' => $concept,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('ResearchAssistantService: Failed to generate illustration', [
                    'concept' => $concept,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Step 4: Scan content for any inline markdown image links
        $images = $this->extractInlineImagesFromContent($content, $images);

        return [
            'success' => true,
            'content' => $content,
            'images' => $images,
        ];
    }

    /**
     * Use the LLM to extract key concepts from the content that should be illustrated.
     * Returns an array of image generation prompts.
     */
    protected function extractIllustrationPrompts(array $parameters, string $content): array
    {
        $user = auth()->user();
        $classificationModel = config('openai.openai.default_model', 'gpt-6.2-sol');

        // Truncate content to avoid token limits on the classification call
        $truncatedContent = mb_substr($content, 0, 3000);

        try {
            $result = $this->chatGPTService->chat([
                [
                    'role' => 'system',
                    'content' => 'You are an illustration concept extractor. Given a user request and the generated text response, identify up to 3 key concepts that would benefit from visual illustration. Return ONLY a JSON array of short image generation prompts (one sentence each), nothing else. Example: ["A detailed diagram showing water molecule structure with hydrogen and oxygen atoms", "A cross-section illustration of plant cell mitosis"].',
                ],
                [
                    'role' => 'user',
                    'content' => "User request: {$parameters['input']}\n\nGenerated response:\n{$truncatedContent}",
                ],
            ], $classificationModel, []);

            $responseContent = trim($result['content'] ?? '');

            // Try to parse as JSON
            $prompts = json_decode($responseContent, true);
            if (is_array($prompts) && ! empty($prompts)) {
                // Clean up prompts - remove any surrounding quotes or whitespace
                return array_map(function ($p) {
                    return trim($p, "\"' \t\n\r");
                }, array_filter($prompts));
            }
        } catch (\Exception $e) {
            Log::warning('ResearchAssistantService: Failed to extract illustration prompts', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback: use the user's input as the image prompt
        return [($parameters['input'] ?? 'Educational illustration')];
    }

    /**
     * Scan response content for markdown image links and merge them into the images array.
     * This ensures images embedded in text (e.g. from mixed responses) are captured.
     * Returns the merged images array and strips the markdown image links from the content
     * (passed by reference) to avoid duplicate rendering.
     */
    protected function extractInlineImagesFromContent(string &$content, array $existingImages): array
    {
        // Match markdown image syntax: ![alt](url)
        preg_match_all('/!\[([^\]]*)\]\(([^)]+)\)/', $content, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $existingImages;
        }

        $urls = collect($existingImages)->pluck('url')->toArray();
        $newImages = [];

        foreach ($matches as $match) {
            $alt = $match[1] ?? 'Generated image';
            $url = $match[2];

            // Skip if already in the images array
            if (in_array($url, $urls, true)) {
                // Still strip the markdown link from content to avoid duplicate rendering
                $content = str_replace($match[0], '', $content);
                continue;
            }

            // If it's a base64 data URI, save it to storage
            if (str_starts_with($url, 'data:image')) {
                $savedUrl = $this->saveBase64Image($url);
                if ($savedUrl !== null) {
                    $url = $savedUrl;
                }
            }

            $newImages[] = [
                'url' => $url,
                'alt' => $alt,
            ];
            $urls[] = $url;
        }

        // Strip all remaining markdown image links from content to avoid duplicate rendering
        $content = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '', $content);
        // Clean up extra whitespace left by removal
        $content = preg_replace("/\n{3,}/", "\n\n", trim($content));

        return array_merge($existingImages, $newImages);
    }

    /**
     * Save a base64 data URI image to storage and return the public URL.
     */
    protected function saveBase64Image(string $dataUri): ?string
    {
        try {
            // Parse the data URI to extract the base64 content and MIME type
            if (!preg_match('/^data:(image\/[a-zA-Z0-9+.\-]+);base64,(.+)$/', $dataUri, $matches)) {
                Log::warning('ResearchAssistantService: Invalid base64 data URI');
                return null;
            }

            $mimeType = $matches[1];
            $base64Data = base64_decode($matches[2]);

            if ($base64Data === false) {
                Log::warning('ResearchAssistantService: Failed to decode base64 image data');
                return null;
            }

            // Determine file extension from MIME type
            $extension = match ($mimeType) {
                'image/png' => 'png',
                'image/jpeg', 'image/jpg' => 'jpg',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'png',
            };

            // Generate a unique filename
            $filename = 'images/ai-generated/' . uniqid('img_', true) . '.' . $extension;

            // Save to public storage
            $fullPath = storage_path('app/public/' . $filename);
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            file_put_contents($fullPath, $base64Data);

            // Return a relative URL so it works regardless of APP_URL
            return '/storage/' . $filename;
        } catch (\Exception $e) {
            Log::error('ResearchAssistantService: Failed to save base64 image', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function prepareImagePrompt($parameters): string
    {
        // Create a detailed prompt for image generation
        $description = $parameters['input'] ?? 'Generate an educational image';

        $details = [];
        if (! empty($parameters['subject']) && is_string($parameters['subject'])) {
            $details[] = 'Subject: ' . $parameters['subject'];
        }

        if (! empty($parameters['topics'])) {
            $details[] = 'Topics: ' . (is_array($parameters['topics']) ? implode(', ', array_filter($parameters['topics'], 'is_string')) : ($parameters['topics'] ?? ''));
        }

        if (! empty($parameters['academic_level']) && is_string($parameters['academic_level'])) {
            $details[] = 'Academic level: ' . $parameters['academic_level'];
        }

        $prompt = $description;
        if (! empty($details)) {
            $prompt .= '. ' . implode('. ', $details);
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

        // Ensure conversation history content is always string
        $safeHistory = [];
        foreach ($conversationHistory as $msg) {
            $safeHistory[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => is_string($msg['content'] ?? '') ? $msg['content'] : (is_array($msg['content']) ? implode(', ', array_filter($msg['content'], 'is_string')) : ''),
            ];
        }
        $messages = array_merge($messages, $safeHistory);
        $messages[] = ['role' => 'user', 'content' => $this->prepareTextPrompt($parameters)];

        $result = $this->chatGPTService->chat($messages, $model, [
            'temperature' => (float) ($parameters['creativity_level'] ?? 1.0),
            'max_output_tokens' => (int) ($parameters['response_length'] ?? 1000000),
            //            'request_type' => $parameters['request_type'] ?? 'chat'
        ]);

        // If the chat response includes images (e.g. from mixed text+image responses),
        // normalize any base64 data URIs by saving them to storage
        $images = $result['images'] ?? [];
        if (!empty($images)) {
            $normalizedImages = [];
            foreach ($images as $image) {
                $url = $image['url'] ?? null;
                if ($url && str_starts_with($url, 'data:image')) {
                    $savedUrl = $this->saveBase64Image($url);
                    if ($savedUrl !== null) {
                        $image['url'] = $savedUrl;
                    }
                }
                $normalizedImages[] = $image;
            }
            $images = $normalizedImages;
        }

        // Also scan the text content for inline markdown image links
        $content = $result['content'] ?? '';
        if (!empty($content)) {
            $images = $this->extractInlineImagesFromContent($content, $images);
            $result['content'] = $content;
        }

        if (!empty($images)) {
            $result['images'] = $images;
        }

        return $result;
    }

    /**
     * Stream text generation — calls the callback with each text delta chunk.
     * Returns the final result array after streaming completes.
     */
    public function streamTextGeneration($parameters, $conversationHistory, callable $chunkCallback): array
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

        // Build request data for streaming
        $requestData = [
            'model' => $model,
            'input' => $messages,
        ];

        if (isset($parameters['creativity_level'])) {
            $requestData['temperature'] = (float) $parameters['creativity_level'];
        }
        if (isset($parameters['response_length'])) {
            $requestData['max_output_tokens'] = (int) $parameters['response_length'];
        }

        $result = $this->chatGPTService->streamChat($requestData, $chunkCallback);

        // Normalize images if any
        $images = $result['images'] ?? [];
        if (!empty($images)) {
            $normalizedImages = [];
            foreach ($images as $image) {
                $url = $image['url'] ?? null;
                if ($url && str_starts_with($url, 'data:image')) {
                    $savedUrl = $this->saveBase64Image($url);
                    if ($savedUrl !== null) {
                        $image['url'] = $savedUrl;
                    }
                }
                $normalizedImages[] = $image;
            }
            $images = $normalizedImages;
        }

        // Scan content for inline markdown image links
        $content = $result['content'] ?? '';
        if (!empty($content)) {
            $images = $this->extractInlineImagesFromContent($content, $images);
            $result['content'] = $content;
        }

        if (!empty($images)) {
            $result['images'] = $images;
        }

        return $result;
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
        if (isset($parameters['academic_level']) && is_string($parameters['academic_level'])) {
            $level = strtolower($parameters['academic_level']);
            $systemPrompt .= "Tailor your responses for {$level} level understanding. ";
        }

        // Academic group/curriculum
        if (isset($parameters['academic_group']) && is_string($parameters['academic_group'])) {
            $systemPrompt .= "Focus on the {$parameters['academic_group']} curriculum standards. ";
        }

        // Subject focus
        if (isset($parameters['subject']) && is_string($parameters['subject'])) {
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
        if (isset($parameters['difficulty']) && is_string($parameters['difficulty'])) {
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
        if (isset($parameters['response_format']) && is_string($parameters['response_format'])) {
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
        $prompt = 'User request: ' . (is_string($parameters['input'] ?? '') ? $parameters['input'] : (is_array($parameters['input'] ?? null) ? implode(', ', array_filter($parameters['input'], 'is_string')) : ''));

        // Add context from parameters
        $context = [];
        foreach ($parameters as $key => $value) {
            if ($key !== 'input' && ! empty($value)) {
                if (is_array($value)) {
                    $context[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . implode(', ', array_filter($value, 'is_string'));
                } elseif (is_string($value)) {
                    $context[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                }
                // skip non-string, non-array values to avoid type conversion errors
            }
        }

        if (! empty($context)) {
            $prompt .= "\n\nContext:\n" . implode("\n", $context);
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

        // Get the appropriate model for this user.
        // In a queue worker context auth()->user() is null — fall back to the
        // configured default so the API call doesn't receive an empty model string.
        $modelToUse = $user
            ? $user->getOpenAiModel()
            : (config('openai.openai.model') ?: 'gpt-6.2-sol');

        // Prepare messages array
        $formattedMessages = [];

        // For quiz generation, DON'T use buildEducationalSystemMessage
        // The prompt already contains all necessary instructions
        if (isset($parameters['request_type']) && $parameters['request_type'] === 'quiz_generation') {
            // Use ONLY the provided prompt - don't add educational context
            if (isset($parameters['input'])) {
                $inputContent = is_string($parameters['input']) ? $parameters['input'] : (is_array($parameters['input']) ? implode(', ', array_filter($parameters['input'], 'is_string')) : '');
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $inputContent,
                ];
            }
        } else {
            // For regular chat, use educational system message
            $systemMessage = $this->buildEducationalSystemMessage($parameters);
            $formattedMessages[] = ['role' => 'system', 'content' => $systemMessage];

            // Add conversation history if provided, ensuring content is always string
            if (! empty($messages)) {
                $safeMessages = [];
                foreach ($messages as $msg) {
                    $safeMessages[] = [
                        'role' => $msg['role'] ?? 'user',
                        'content' => is_string($msg['content'] ?? '') ? $msg['content'] : (is_array($msg['content']) ? implode(', ', array_filter($msg['content'], 'is_string')) : ''),
                    ];
                }
                $formattedMessages = array_merge($formattedMessages, $safeMessages);
            }

            // Add current user message if provided in parameters
            if (isset($parameters['input'])) {
                $inputContent = is_string($parameters['input']) ? $parameters['input'] : (is_array($parameters['input']) ? implode(', ', array_filter($parameters['input'], 'is_string')) : '');
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $inputContent,
                ];
            }
        }

        // Build request data with educational parameters
        $requestData = [
            'model' => $modelToUse,
            'input' => $formattedMessages,
            'temperature' => (float) ($parameters['creativity_level'] ?? 1.0),
        ];

        $tokenLimit = (int) ($parameters['response_length'] ?? 1000000);
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
                return "File uploaded:\n" .
                    '- Name: ' . $originalName . "\n" .
                    '- Size: ' . $file->getSize() . " bytes\n" .
                    '- Type: ' . $mimeType . "\n" .
                    'Note: Content could not be automatically extracted from this file type.';
            }
        } catch (Exception $e) {
            \Log::error('File extraction error', [
                'error' => $e->getMessage(),
                'file' => $originalName,
                'mime_type' => $mimeType,
            ]);

            // Return a more informative error message
            return 'Error processing file: ' . $originalName . "\n" .
                'Issue: ' . $e->getMessage() . "\n" .
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

            return "PDF file content extraction failed.\n" .
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n" .
                'Error: ' . $e->getMessage();
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
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n";
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
            return "Word document (.docx) uploaded.\n" .
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n";
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
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n";
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
                    return "Excel spreadsheet content (text values only):\n" . trim($content);
                }
            }

            // Fallback if XML parsing fails
            return "Excel document (.xlsx) uploaded.\n" .
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n";
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
            default => "Unsupported file type: {$extension}\n" .
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n"
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
            $html = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $html .= $this->elementToHtml($element);
                }
            }

            return trim($html);
        } catch (Exception $e) {
            Log::error('DOCX processing failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return 'Error processing document: ' . $e->getMessage() . "\n" .
                'File name: ' . $file->getClientOriginalName() . "\n" .
                'File size: ' . $file->getSize() . " bytes\n";
        }
    }

    /**
     * Convert PhpWord element to HTML
     */
    protected function elementToHtml($element): string
    {
        $html = '';

        if (method_exists($element, 'getText')) {
            $text = $element->getText();
            $style = method_exists($element, 'getFontStyle') ? $element->getFontStyle() : null;

            if ($style) {
                $styleAttr = [];
                if (method_exists($style, 'isBold') && $style->isBold()) {
                    $text = '<strong>' . htmlspecialchars($text) . '</strong>';
                } else {
                    $text = htmlspecialchars($text);
                }
                if (method_exists($style, 'isItalic') && $style->isItalic()) {
                    $text = '<em>' . $text . '</em>';
                }
                if (method_exists($style, 'getUnderline') && $style->getUnderline() !== 'none') {
                    $text = '<u>' . $text . '</u>';
                }
                $html .= $text;
            } else {
                $html .= htmlspecialchars($text);
            }
            $html .= "\n";
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $html .= $this->elementToHtml($childElement);
            }
        }

        return $html;
    }
}
