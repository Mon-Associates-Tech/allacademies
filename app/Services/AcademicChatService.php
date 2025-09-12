<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AcademicChatService
{
    protected mixed $apiKey;
    protected string $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('openai.openai.api_key');
    }

    /**
     * Generate educational chat response with context parameters
     */
    public function chat(array $parameters, array $messages = [], string $model = 'gpt-4.1-nano'): array
    {
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
            'model' => $model,
            'messages' => $formattedMessages,
            'temperature' => (float) $parameters['creativity_level'] ?? 0.7,
            'max_tokens' => (int) $parameters['response_length'] ?? 1000,
        ];

        // Add additional OpenAI parameters if specified
        if (isset($parameters['top_p'])) {
            $requestData['top_p'] = $parameters['top_p'];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->endpoint, $requestData);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'content' => $responseData['choices'][0]['message']['content'],
                    'usage' => $responseData['usage'] ?? null,
                    'model' => $responseData['model'] ?? $model
                ];
            }

            Log::error('OpenAI API Error', ['response' => $response->body()]);
            return [
                'success' => false,
                'error' => "API Error: " . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Educational Chat Service Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Service temporarily unavailable. Please try again.'
            ];
        }
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

        return $systemPrompt;
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
        if (isset($parameters['creativity_level']) && (!is_numeric($parameters['creativity_level']) || $parameters['creativity_level'] < 0 || $parameters['creativity_level'] > 1)) {
            $errors[] = 'Creativity level must be between 0 and 1';
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
}
