<?php

namespace App\Livewire;

use App\Models\AcademicChatMessage;
use App\Services\AcademicChatService;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class AcademicChat extends Component
{
    use WithFileUploads;

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

    #[Rule('nullable|file|max:10240')] // 10MB limit
    public $uploadedFile = null;

    public $fileContent = '';
    public $fileName = '';

    // Component state
    public $messages = [];
    public $isLoading = false;
    public $showParameters = false;
    public $availableSubjects = [];
    public $availableTopics = [];
    public $errors = [];
    public $conversationId = null;
    public $conversationHistory = []; // This needs to be a simple array, not associative
    public $showHistory = false;

    protected $chatService;

    public function boot(AcademicChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->availableSubjects = $this->chatService->getAvailableSubjects();
        $this->loadConversationHistory();
        $this->loadChatHistory();

        // Set default values
        $this->difficulty = 'medium';
        $this->response_format = 'detailed';
        $this->creativity_level = 0.7;
        $this->response_length = 1000;
    }

    public function sendMessage(): void
    {
        $this->validate();

        if (empty(trim($this->message))) {
            return;
        }

        $this->isLoading = true;
        $this->errors = [];

        // Generate conversation ID if this is a new conversation
        if (!$this->conversationId) {
            $this->conversationId = (string) Str::uuid();
        }

        // Prepare parameters
        $parameters = $this->getParameters();
        $parameters['message'] = $this->message;

        if (!empty($this->fileContent)) {
            $parameters['file_content'] = $this->fileContent;
        }

        // Validate parameters
        $validationErrors = $this->chatService->validateParameters($parameters);
        if (!empty($validationErrors)) {
            $this->errors = $validationErrors;
            $this->isLoading = false;
            return;
        }

        // Process uploaded file if present
        if ($this->uploadedFile) {
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
            $this->uploadedFile = null; // Reset file input
        }

        // Add user message to chat and database
        $userMessageContent = $this->message;
        if (!empty($this->fileContent)) {
            $userMessageContent .= "\n\nFile: " . $this->fileName . "\nFile Content:\n" . $this->fileContent;
        }
        // Add user message to chat and database
        $userMessage = [
            'role' => 'user',
            'content' => $userMessageContent,
            'timestamp' => now()->toISOString()
        ];
        $this->messages[] = $userMessage;

        // Save user message to database
        AcademicChatMessage::create([
            'user_id' => Auth::id(),
            'conversation_id' => $this->conversationId,
            'conversation_title' => $this->generateConversationTitle(),
            'content' => $this->message,
            'role' => 'user',
            'parameters' => $parameters
        ]);

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

            // Save AI response to database
            AcademicChatMessage::create([
                'user_id' => Auth::id(),
                'conversation_id' => $this->conversationId,
                'conversation_title' => $this->generateConversationTitle(),
                'content' => $response['content'],
                'role' => 'assistant',
                'parameters' => $parameters,
                'usage' => $response['usage'] ?? null
            ]);
        } else {
            $this->errors[] = $response['error'];
        }

        // Clear message and reset loading
        $this->message = '';
        $this->fileContent = '';
        $this->fileName = '';
        $this->isLoading = false;

        // Refresh conversation history
        $this->loadConversationHistory();
    }

    public function updatedUploadedFile()
    {
        $this->validateOnly('uploadedFile');

        if ($this->uploadedFile) {
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
        }
    }
    public function clearChat()
    {
        $this->messages = [];
        $this->conversationId = null;
    }

    public function loadConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        $this->loadChatHistory();
    }

    public function deleteConversation($conversationId)
    {
        AcademicChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $conversationId)
            ->delete();

        if ($this->conversationId === $conversationId) {
            $this->messages = [];
            $this->conversationId = null;
        }

        $this->loadConversationHistory();
    }

    public function newConversation()
    {
        $this->messages = [];
        $this->conversationId = null;
    }

    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    protected function loadChatHistory()
    {
        if ($this->conversationId) {
            // Load chat history from database for specific conversation
            $dbMessages = AcademicChatMessage::where('user_id', Auth::id())
                ->where('conversation_id', $this->conversationId)
                ->orderBy('created_at', 'asc')
                ->get();

            $this->messages = $dbMessages->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'timestamp' => $msg->created_at->toISOString(),
                    'usage' => $msg->usage
                ];
            })->toArray();
        } else {
            $this->messages = [];
        }
    }

    protected function loadConversationHistory()
    {
        // Load conversation history from database as a simple array
        $conversations = AcademicChatMessage::select('conversation_id', 'conversation_title', 'created_at')
            ->where('user_id', Auth::id())
            ->whereNotNull('conversation_id')
            ->groupBy('conversation_id', 'conversation_title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Convert to simple array format that Livewire supports
        $this->conversationHistory = $conversations->map(function ($conversation) {
            return [
                'id' => $conversation->conversation_id,
                'title' => $conversation->conversation_title ?? 'Untitled Conversation',
                'created_at' => $conversation->created_at
            ];
        })->toArray();
    }

    protected function generateConversationTitle()
    {
        if (!empty($this->messages)) {
            // Use the first user message as the title
            $firstUserMessage = collect($this->messages)->firstWhere('role', 'user');
            if ($firstUserMessage) {
                return Str::limit($firstUserMessage['content'], 50);
            }
        }

        // Fallback title
        return 'Academic Chat - ' . now()->format('M j, Y');
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

    public function render()
    {
        return view('livewire.academic-chat');
    }
}
