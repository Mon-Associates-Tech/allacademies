<?php

namespace App\Livewire;

use App\Models\AcademicChatMessage;
use App\Services\ResearchAssistantService;
use App\Traits\ChecksTokenAvailability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResearchAssistant extends Component
{
    use ChecksTokenAvailability;
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

    #[Rule('nullable|numeric|min:1|max:2')]
    public $creativity_level = 1;

    #[Rule('nullable|integer|min:100|max:2000')]
    public $response_length = 1000;

    #[Rule('nullable|file|max:10240')]
    public $uploadedFile;

    public $fileContent = '';

    public $fileName = '';

    // Component state
    public $messages = [];

    public $isLoading = false;

    public $showParameters = false;

    public $showHistory = false;

    public $availableSubjects = [];

    public $availableTopics = [];

    public $errors = [];

    public $conversationId;

    public $conversationTitle;

    public $conversationHistory = [];

    public $hasAvailableTokens = true;

    public $tokenMessage;

    #[Rule('nullable|string|uuid')]
    public $urlConversationId;

    protected $chatService;

    public function boot(ResearchAssistantService $chatService): void
    {
        $this->chatService = $chatService;
    }

    public function mount(?string $conversationId = null): void
    {
        $result = $this->checkTokenAvailability();
        $this->hasAvailableTokens = $result['available'];
        $this->tokenMessage = $result['message'];

        $this->availableSubjects = $this->chatService->getAvailableSubjects();

        // Check URL query parameter first
        $urlConversationId = request()->query('conversationId');

        if ($urlConversationId) {
            $this->conversationId = $urlConversationId;
            $this->urlConversationId = $urlConversationId;
            $this->loadChatHistory();
            $this->loadConversationTitle();
        } elseif ($conversationId) {
            // Fallback for route parameter
            $this->conversationId = $conversationId;
            $this->urlConversationId = $conversationId;
            $this->loadChatHistory();
            $this->loadConversationTitle();
        }

        $this->loadConversationHistory();

        // Set default values
        $this->difficulty = 'medium';
        $this->response_format = 'detailed';
        $this->creativity_level = 1;
        $this->response_length = 1000;
    }

    protected function loadChatHistory(): void
    {
        if (! $this->conversationId) {
            $this->messages = [];

            return;
        }

        // Clear messages first
        $this->messages = [];

        // Fresh query from database
        $dbMessages = AcademicChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $this->conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($dbMessages->isEmpty()) {
            return;
        }

        // Build messages array
        $messages = [];
        foreach ($dbMessages as $msg) {
            $content = $msg->content;

            if ($msg->role === 'assistant') {
                $content = $this->normalizeStoredAssistantContent($msg->content);
            }

            $messages[] = [
                'role' => $msg->role,
                'content' => $content,
                'timestamp' => $msg->created_at->toISOString(),
                'usage' => $msg->usage,
                'images' => $msg->images ?? null,
                'model_used' => $msg->model_used ?? null,
            ];
        }

        // Assign all at once
        $this->messages = $messages;
    }

    protected function normalizeStoredAssistantContent(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $segments = [];

            if (isset($decoded[0]['content']) && is_array($decoded[0]['content'])) {
                foreach ($decoded[0]['content'] as $segment) {
                    if (is_string($segment)) {
                        $segments[] = $segment;

                        continue;
                    }

                    if (isset($segment['text']) && is_string($segment['text'])) {
                        $segments[] = $segment['text'];
                    }
                }
            }

            $fallback = trim(json_encode($decoded));
            $joined = trim(implode("\n\n", array_filter($segments, static fn ($segment) => trim($segment) !== '')));

            return $joined !== '' ? $joined : $fallback;
        }

        return $content;
    }

    protected function loadConversationTitle(): void
    {
        if (! $this->conversationId) {
            $this->conversationTitle = null;

            return;
        }

        $firstMessage = AcademicChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $this->conversationId)
            ->orderBy('created_at', 'asc')
            ->first();

        $this->conversationTitle = $firstMessage?->conversation_title ?? 'Untitled Conversation';
    }

    protected function loadConversationHistory(): void
    {
        $conversations = AcademicChatMessage::where('user_id', Auth::id())
            ->whereNotNull('conversation_id')
            ->selectRaw('conversation_id, conversation_title, MAX(created_at) as created_at')
            ->groupBy('conversation_id', 'conversation_title')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $this->conversationHistory = $conversations->map(function ($conversation) {
            return [
                'id' => $conversation->conversation_id,
                'title' => $conversation->conversation_title ?? 'Untitled Conversation',
                'created_at' => $conversation->created_at,
            ];
        })->toArray();
    }

    #[Computed]
    public function currentTokenWarning(): ?string
    {
        return $this->getTokenWarningMessage();
    }

    public function sendMessage(): void
    {
        if ($this->messageInputDisabled()) {
            $this->dispatch('tokenCheckFailed');
            logError('sendMessage input disabled');

            return;
        }

        $this->validate();

        if (empty(trim($this->message))) {
            return;
        }
        $this->isLoading = true;
        $this->errors = [];

        if (! $this->conversationId) {
            $this->conversationId = (string) Str::uuid();
            $this->urlConversationId = $this->conversationId;
        }

        $parameters = $this->getParameters();
        $parameters['input'] = $this->message;

        if (! empty($this->fileContent)) {
            $parameters['file_content'] = $this->fileContent;
        }

        $validationErrors = $this->chatService->validateParameters($parameters);
        if (! empty($validationErrors)) {
            $this->errors = $validationErrors;
            $this->isLoading = false;

            return;
        }

        if ($this->uploadedFile) {
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
            $this->uploadedFile = null;
        }

        $userMessageContent = $this->message;
        if (! empty($this->fileContent)) {
            $userMessageContent .= "\n\nFile: ".$this->fileName."\nFile Content:\n".$this->fileContent;
        }

        $userMessage = [
            'role' => 'user',
            'content' => $userMessageContent,
            'timestamp' => now()->toISOString(),
        ];
        $this->messages[] = $userMessage;

        $title = $this->generateConversationTitle();
        $this->conversationTitle = $title;

        AcademicChatMessage::create([
            'user_id' => Auth::id(),
            'conversation_id' => $this->conversationId,
            'conversation_title' => $title,
            'content' => $this->message,
            'role' => 'user',
            'parameters' => $parameters,
        ]);

        $conversationHistory = $this->getConversationHistory();
        $response = $this->chatService->processRequest($parameters, $conversationHistory);

        if ($response['success']) {
            $aiMessage = [
                'role' => 'assistant',
                'content' => $response['content'],
                'timestamp' => now()->toISOString(),
                'usage' => $response['usage'] ?? null,
                'images' => $response['images'] ?? null,
                'model_used' => $response['model_used'] ?? null,
            ];
            $this->messages[] = $aiMessage;

            AcademicChatMessage::create([
                'user_id' => Auth::id(),
                'conversation_id' => $this->conversationId,
                'conversation_title' => $title,
                'content' => $response['content'],
                'role' => 'assistant',
                'parameters' => $parameters,
                'usage' => $response['usage'] ?? null,
                'model_used' => $response['model_used'] ?? null,
                'images' => $response['images'] ?? null,
            ]);
        } else {
            $this->errors[] = $response['error'] ?? 'Unknown error occurred';
        }

        $this->message = '';
        $this->fileContent = '';
        $this->fileName = '';
        $this->isLoading = false;

        $this->loadConversationHistory();
    }

    #[Computed]
    public function messageInputDisabled(): bool
    {
        return !$this->hasAvailableTokens;
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
            return $value !== null && $value !== '' && (! is_array($value) || ! empty($value));
        });
    }

    protected function generateConversationTitle(): string
    {
        if (! empty($this->messages)) {
            $firstUserMessage = collect($this->messages)->firstWhere('role', 'user');
            if ($firstUserMessage) {
                return Str::limit($firstUserMessage['content'], 50);
            }
        }

        return 'Academic Chat - '.now()->format('M j, Y');
    }

    protected function getConversationHistory(): array
    {
        $history = [];

        foreach ($this->messages as $msg) {
            $history[] = [
                'role' => $msg['role'],
                'content' => is_string($msg['content']) ? $msg['content'] : (string) $msg['content'],
            ];
        }

        return array_slice($history, -10);
    }

    public function updatedUploadedFile(): void
    {
        $this->validateOnly('uploadedFile');

        if ($this->uploadedFile) {
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
        }
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->conversationId = null;
        $this->urlConversationId = null;
        $this->conversationTitle = null;
        $this->redirect(route('research-assistant.index'));
    }

    public function loadConversation($conversationId): void
    {
        // Force complete state reset
        $this->reset('messages', 'conversationId', 'conversationTitle');

        // Now set the new conversation
        $this->conversationId = $conversationId;
        $this->urlConversationId = $conversationId;

        // Load fresh data from database
        $this->loadConversationTitle();
        $this->loadChatHistory();

        // Redirect to update URL with query parameter
        $this->redirect(route('research-assistant.index', ['conversationId' => $conversationId]));
    }

    public function deleteConversation($conversationId): void
    {
        AcademicChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $conversationId)
            ->delete();

        if ($this->conversationId === $conversationId) {
            $this->messages = [];
            $this->conversationId = null;
            $this->urlConversationId = null;
            $this->conversationTitle = null;
        }

        $this->loadConversationHistory();
    }

    public function newConversation(): void
    {
        $this->messages = [];
        $this->conversationId = null;
        $this->urlConversationId = null;
        $this->conversationTitle = null;

        // Redirect to clean URL
        $this->redirect(route('research-assistant.index'));
    }

    public function toggleHistory(): void
    {
        $this->showHistory = ! $this->showHistory;
    }

    public function toggleParameters(): void
    {
        $this->showParameters = ! $this->showParameters;
    }

    public function updatedSubject(): void
    {
        $this->availableTopics = $this->availableSubjects[$this->subject] ?? [];
        $this->topics = [];
        $this->subtopics = [];
    }

    public function addTopic($topic): void
    {
        if (! in_array($topic, $this->topics, true)) {
            $this->topics[] = $topic;
        }
    }

    public function removeTopic($index): void
    {
        unset($this->topics[$index]);
        $this->topics = array_values($this->topics);
    }

    public function addSubtopic(): void
    {
        // Handled by Alpine.js
    }

    public function removeSubtopic($index): void
    {
        unset($this->subtopics[$index]);
        $this->subtopics = array_values($this->subtopics);
    }

    public function addAccommodation($accommodation): void
    {
        if (! in_array($accommodation, $this->accommodations, true)) {
            $this->accommodations[] = $accommodation;
        }
    }

    public function removeAccommodation($index): void
    {
        unset($this->accommodations[$index]);
        $this->accommodations = array_values($this->accommodations);
    }

    public function resetParameters(): void
    {
        $this->reset([
            'age', 'academic_level', 'academic_group', 'subject',
            'topics', 'subtopics', 'learning_style', 'difficulty',
            'accommodations', 'response_format', 'creativity_level', 'response_length',
        ]);

        $this->difficulty = 'medium';
        $this->response_format = 'detailed';
        $this->creativity_level = 1;
        $this->response_length = 1000;
    }

    public function render()
    {
        return view('livewire.research-assistant');
    }
}
