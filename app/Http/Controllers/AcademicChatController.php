<?php

namespace App\Http\Controllers;

use App\Services\AcademicChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicChatController extends Controller
{
    protected AcademicChatService $chatService;

    public function __construct(AcademicChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Display the chat interface
     */
    public function index()
    {
        return view('chats.gpt-chat');
    }

    /**
     * Handle API chat requests
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'age' => 'nullable|integer|min:5|max:100',
            'academic_level' => 'nullable|string|in:elementary,middle_school,high_school,college,graduate',
            'academic_group' => 'nullable|string|max:100',
            'subject' => 'nullable|string|max:100',
            'topics' => 'nullable|array',
            'topics.*' => 'string|max:100',
            'subtopics' => 'nullable|array',
            'subtopics.*' => 'string|max:100',
            'learning_style' => 'nullable|string|in:visual,auditory,kinesthetic,reading',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'accommodations' => 'nullable|array',
            'accommodations.*' => 'string|in:simplified_language,step_by_step,examples_heavy',
            'response_format' => 'nullable|string|in:detailed,concise,interactive',
            'creativity_level' => 'nullable|numeric|min:0|max:1',
            'response_length' => 'nullable|integer|min:100|max:2000',
            'conversation_history' => 'nullable|array',
            'conversation_history.*.role' => 'required|string|in:user,assistant',
            'conversation_history.*.content' => 'required|string',
            'model' => 'nullable|string|in:gpt-3.5-turbo,gpt-4,gpt-4-turbo-preview',
        ]);

        // Validate educational parameters
        $parameterErrors = $this->chatService->validateParameters($validated);
        if (! empty($parameterErrors)) {
            return response()->json([
                'success' => false,
                'errors' => $parameterErrors,
            ], 422);
        }

        // Get conversation history
        $conversationHistory = $validated['conversation_history'] ?? [];

        // Send to chat service
        $result = $this->chatService->chat(
            $validated,
            $conversationHistory,
            $validated['model'] ?? 'gpt-4'
        );

        return response()->json($result);
    }

    /**
     * Get available subjects and topics
     */
    public function subjects(): JsonResponse
    {
        return response()->json([
            'subjects' => $this->chatService->getAvailableSubjects(),
        ]);
    }

    /**
     * Get learning recommendations based on parameters
     */
    public function recommendations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'age' => 'nullable|integer|min:5|max:100',
            'academic_level' => 'nullable|string',
            'subject' => 'nullable|string',
            'learning_style' => 'nullable|string',
        ]);

        $recommendations = $this->generateRecommendations($validated);

        return response()->json([
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Generate personalized learning recommendations
     */
    protected function generateRecommendations(array $parameters): array
    {
        $recommendations = [];

        // Age-based recommendations
        if (isset($parameters['age'])) {
            $age = $parameters['age'];
            if ($age <= 12) {
                $recommendations['topics'] = ['basic_math', 'reading', 'science_basics'];
                $recommendations['format'] = 'interactive';
                $recommendations['accommodations'] = ['simplified_language', 'examples_heavy'];
            } elseif ($age <= 16) {
                $recommendations['topics'] = ['algebra', 'literature', 'biology'];
                $recommendations['format'] = 'detailed';
                $recommendations['accommodations'] = ['step_by_step'];
            } elseif ($age <= 18) {
                $recommendations['topics'] = ['calculus', 'advanced_literature', 'chemistry'];
                $recommendations['format'] = 'detailed';
                $recommendations['difficulty'] = 'advanced';
            } else {
                $recommendations['topics'] = ['specialized_subjects'];
                $recommendations['format'] = 'interactive';
                $recommendations['difficulty'] = 'advanced';
            }
        }

        // Learning style recommendations
        if (isset($parameters['learning_style'])) {
            switch ($parameters['learning_style']) {
                case 'visual':
                    $recommendations['tips'] = [
                        'Request diagrams and visual explanations',
                        'Ask for concept maps',
                        'Use color-coding for different concepts',
                    ];
                    break;
                case 'auditory':
                    $recommendations['tips'] = [
                        'Ask for step-by-step verbal explanations',
                        'Request analogies and stories',
                        'Use discussion-based learning',
                    ];
                    break;
                case 'kinesthetic':
                    $recommendations['tips'] = [
                        'Request hands-on activities',
                        'Ask for real-world applications',
                        'Use practical examples',
                    ];
                    break;
                case 'reading':
                    $recommendations['tips'] = [
                        'Request detailed written explanations',
                        'Ask for additional reading materials',
                        'Use structured information',
                    ];
                    break;
            }
        }

        return $recommendations;
    }

    /**
     * Export chat history
     */
    public function exportChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => 'required|array',
            'format' => 'nullable|string|in:json,txt,pdf',
        ]);

        $messages = $validated['messages'];
        $format = $validated['format'] ?? 'json';

        switch ($format) {
            case 'txt':
                $content = $this->exportToText($messages);
                $mimeType = 'text/plain';
                break;
            case 'pdf':
                // You would implement PDF generation here
                return response()->json(['error' => 'PDF export not implemented yet'], 501);
            default:
                $content = json_encode($messages, JSON_PRETTY_PRINT);
                $mimeType = 'application/json';
        }

        return response()->json([
            'content' => $content,
            'filename' => 'chat_export_'.date('Y-m-d_H-i-s').'.'.($format === 'json' ? 'json' : 'txt'),
            'mime_type' => $mimeType,
        ]);
    }

    /**
     * Convert messages to text format
     */
    protected function exportToText(array $messages): string
    {
        $text = "Educational Chat Export\n";
        $text .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $text .= str_repeat('=', 50)."\n\n";

        foreach ($messages as $message) {
            $role = ucfirst($message['role']);
            $timestamp = isset($message['timestamp']) ? date('H:i:s', strtotime($message['timestamp'])) : '';
            $text .= "{$role} [{$timestamp}]:\n";
            $text .= $message['content']."\n\n";
        }

        return $text;
    }
}
