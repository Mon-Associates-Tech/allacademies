<?php

namespace App\Livewire;

use App\Services\AcademicChatService;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Session;

class AcademicChat extends Component
{
    // Chat parameters
    #[Rule('required|string|max:1000')]
    public $message = '';

    #[Rule('nullable|integer|min:5|max:100')]
    public $age = null;

    #[Rule('nullable|string')]
    public $academic_level = '';

    #[Rule('nullable|string')]
    public $academic_group = '';

    #[Rule('nullable|string')]
    public $subject = '';

    public $topics = [];
    public $subtopics = [];

    #[Rule('nullable|string')]
    public $learning_style = '';

    #[Rule('nullable|string')]
    public $difficulty = 'medium';

    public $accommodations = [];

    #[Rule('nullable|string')]
    public $response_format = 'detailed';

    #[Rule('nullable|numeric|min:0|max:1')]
    public $creativity_level = 0.7;

    #[Rule('nullable|integer|min:100|max:2000')]
    public $response_length = 1000;

    // Component state
    public $messages = [];
    public $isLoading = false;
    public $showParameters = false;
    public $availableSubjects = [];
    public $availableTopics = [];
    public $sessionId;
    public $errors = [];

    protected $chatService;

    public function boot(AcademicChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->sessionId = Session::getId();
        $this->availableSubjects = $this->chatService->getAvailableSubjects();
        $this->loadChatHistory();

        // Set default values
        $this->difficulty = 'medium';
        $this->response_format = 'detailed';
        $this->creativity_level = 0.7;
        $this->response_length = 1000;
    }

    public function sendMessage()
    {
        $this->validate();

        if (empty(trim($this->message))) {
            return;
        }

        $this->isLoading = true;
        $this->errors = [];

        // Prepare parameters
        $parameters = $this->getParameters();
        $parameters['message'] = $this->message;

        // Validate parameters
        $validationErrors = $this->chatService->validateParameters($parameters);
        if (!empty($validationErrors)) {
            $this->errors = $validationErrors;
            $this->isLoading = false;
            return;
        }

        // Add user message to chat
        $userMessage = [
            'role' => 'user',
            'content' => $this->message,
            'timestamp' => now()->toISOString()
        ];
        $this->messages[] = $userMessage;

        // Get conversation history for context
        $conversationHistory = $this->getConversationHistory();

        // Send to AI service
        $response = $this->chatService->chat($parameters, $conversationHistory, 'gpt-4.1-nano');

        if ($response['success']) {
            // Add AI response to chat
            $aiMessage = [
                'role' => 'assistant',
                'content' => $response['content'],
                'timestamp' => now()->toISOString(),
                'usage' => $response['usage'] ?? null
            ];
            $this->messages[] = $aiMessage;

            // Save chat history
            $this->saveChatHistory();
        } else {
            $this->errors[] = $response['error'];
        }

        // Clear message and reset loading
        $this->message = '';
        $this->isLoading = false;
    }

    public function clearChat()
    {
        $this->messages = [];
        $this->clearChatHistory();
    }

    public function toggleParameters()
    {
        $this->showParameters = !$this->showParameters;
    }

    public function updatedSubject()
    {
        $this->availableTopics = $this->availableSubjects[$this->subject] ?? [];
        $this->topics = [];
        $this->subtopics = [];
    }

    public function addTopic($topic)
    {
        if (!in_array($topic, $this->topics)) {
            $this->topics[] = $topic;
        }
    }

    public function removeTopic($index)
    {
        unset($this->topics[$index]);
        $this->topics = array_values($this->topics);
    }

    public function addSubtopic()
    {
        // This will be handled by Alpine.js in the frontend
    }

    public function removeSubtopic($index)
    {
        unset($this->subtopics[$index]);
        $this->subtopics = array_values($this->subtopics);
    }

    public function addAccommodation($accommodation)
    {
        if (!in_array($accommodation, $this->accommodations)) {
            $this->accommodations[] = $accommodation;
        }
    }

    public function removeAccommodation($index)
    {
        unset($this->accommodations[$index]);
        $this->accommodations = array_values($this->accommodations);
    }

    public function resetParameters()
    {
        $this->reset([
            'age', 'academic_level', 'academic_group', 'subject',
            'topics', 'subtopics', 'learning_style', 'difficulty',
            'accommodations', 'response_format', 'creativity_level', 'response_length'
        ]);

        // Reset to defaults
        $this->difficulty = 'medium';
        $this->response_format = 'detailed';
        $this->creativity_level = 0.7;
        $this->response_length = 1000;
    }

    protected function getParameters(): array
    {
        return array_filter([
            'age' => $this->age,
            'academic_level' => $this->academic_level,
            'academic_group' => $this->academic_group,
            'subject' => $this->subject,
            'topics' => $this->topics,
            'subtopics' => $this->subtopics,
            'learning_style' => $this->learning_style,
            'difficulty' => $this->difficulty,
            'accommodations' => $this->accommodations,
            'response_format' => $this->response_format,
            'creativity_level' => $this->creativity_level,
            'response_length' => $this->response_length,
        ], function ($value) {
            return $value !== null && $value !== '' && (!is_array($value) || !empty($value));
        });
    }

    protected function getConversationHistory(): array
    {
        // Return last 10 messages for context (excluding current message)
        $history = array_slice($this->messages, -10);
        return array_map(function ($msg) {
            return [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }, $history);
    }

    protected function loadChatHistory()
    {
        $this->messages = Session::get("chat_history_{$this->sessionId}", []);
    }

    protected function saveChatHistory()
    {
        Session::put("chat_history_{$this->sessionId}", $this->messages);
    }

    protected function clearChatHistory()
    {
        Session::forget("chat_history_{$this->sessionId}");
    }

    public function render()
    {
        return view('livewire.academic-chat');
    }
}
