<?php

namespace App\Livewire;

use App\Jobs\ProcessResearchAssistantMessageJob;
use App\Models\AcademicChatMessage;
use App\Services\ResearchAssistantService;
use App\Traits\ChecksTokenAvailability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;
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

    #[Rule('nullable|integer|min:100|max:4000')]
    public $response_length = 4000;

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

    #[Url(as: 'conversationId', history: true)]
    public $conversationId;

    public $conversationTitle;

    public $conversationHistory = [];

    public $hasAvailableTokens = true;

    public $tokenMessage;

    public ?int $pendingUserMessageId = null;

    public ?int $pendingMessageSentAt = null;

    #[Rule('nullable|string|uuid')]
    public $urlConversationId;

    // Streaming support
    public $streamedAnswer = '';

    // Track if current request is image generation (uses queue job + polling)
    public $isImageRequest = false;

    // Timeout in seconds after which we give up waiting for a response
    // ChatGPTService has 3 retries with 300s timeout each + exponential backoff (~10s + 20s)
    // So max wait = 3 * 300 + 30 = 930s, but we set a reasonable limit
    protected const RESPONSE_TIMEOUT_SECONDS = 1000;

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
        $this->response_length = 4000;
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
                'incomplete' => $msg->incomplete ?? false,
            ];
        }

        // Assign all at once
        $this->messages = $messages;
    }

 protected function normalizeStoredAssistantContent(string|array|null $content): string
{
    if (empty($content)) {
        return '';
    }

    // Ensure we are working with a string for json_decode
    $contentString = is_array($content) ? json_encode($content) : $content;
    $decoded = json_decode($contentString, true);

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
        $joined = trim(implode("\n", array_filter($segments, static fn ($segment) => trim($segment) !== '')));
        return $joined !== '' ? $joined : $fallback;
    }

    return $contentString;
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
            return;
        }

        $this->validate();

        if (empty(trim($this->message))) {
            return;
        }

        $this->errors = [];

        if (! $this->conversationId) {
            $this->conversationId = (string) Str::uuid();
            $this->urlConversationId = $this->conversationId;
        }

        $parameters = $this->getParameters();
        $parameters['input'] = $this->message;

        if ($this->uploadedFile) {
            $this->fileContent = $this->chatService->extractFileContent($this->uploadedFile);
            $this->fileName = $this->uploadedFile->getClientOriginalName();
            $this->uploadedFile = null;
        }

        if (! empty($this->fileContent)) {
            $parameters['input'] .= "\n\nFile: ".$this->fileName."\nFile Content:\n".$this->fileContent;
        }

        $validationErrors = $this->chatService->validateParameters($parameters);
        if (! empty($validationErrors)) {
            $this->errors = $validationErrors;
            return;
        }

        $title = $this->generateConversationTitle();
        $this->conversationTitle = $title;

        $userRow = AcademicChatMessage::create([
            'user_id'            => Auth::id(),
            'conversation_id'    => $this->conversationId,
            'conversation_title' => $title,
            'content'            => $parameters['input'],
            'role'               => 'user',
            'parameters'         => $parameters,
        ]);

        $this->messages[] = [
            'role'      => 'user',
            'content'   => $parameters['input'],
            'timestamp' => now()->toISOString(),
        ];

        $this->pendingUserMessageId = $userRow->id;
        $this->pendingMessageSentAt = time();
        $this->isLoading = true;
        $this->message = '';
        $this->fileContent = '';
        $this->fileName = '';

        // Detect if this is a pure image request (no text explanation expected)
        // Only route to queue job when the user clearly wants ONLY an image, not text+images
        $input = $parameters['input'] ?? '';
        $this->isImageRequest = (bool) preg_match('/^(draw|create|generate|make)\s+(an?\s+)?(image|picture|photo|illustration|drawing|sketch|render)/i', $input);

        if ($this->isImageRequest) {
            // Pure image generation uses queue job (can't stream images the same way)
            ProcessResearchAssistantMessageJob::dispatch(
                $userRow->id,
                Auth::id(),
                $this->conversationId,
                $title,
                $parameters,
                $this->getConversationHistory(),
            );
        } else {
            // Text (and mixed text+images) uses streaming — trigger via JS to keep the HTTP connection open
            $this->streamedAnswer = '';
            $this->js('$wire.ask()');
            return; // Don't load conversation history yet; ask() will handle it
        }

        $this->loadConversationHistory();
        $this->dispatch('scroll-to-bottom');
    }

    /**
     * Stream a text response from the AI and save it when complete.
     * Called via $this->js('$wire.ask()') from sendMessage() to keep the HTTP connection open.
     */
    public function ask(): void
    {
        $parameters = $this->getParameters();
        // Restore the input from the user message we just saved
        if ($this->pendingUserMessageId) {
            $userRow = AcademicChatMessage::find($this->pendingUserMessageId);
            if ($userRow) {
                $parameters['input'] = $userRow->content;
            }
        }

        $conversationHistory = $this->getConversationHistory();

        // Stream the response, pushing each chunk to the browser
        $firstChunk = true;
        $result = $this->chatService->streamTextGeneration(
            $parameters,
            $conversationHistory,
            function ($chunk) use (&$firstChunk) {
                // Convert markdown to HTML for streaming display
                $html = $this->markdownToHtml($chunk);
                $this->stream(to: 'streamedAnswer', content: $html);
                // Signal Alpine that streaming has started (only on first chunk)
                if ($firstChunk) {
                    $firstChunk = false;
                    $this->dispatch('stream-started');
                }
            }
        );

        if ($result['success']) {
            // Check if the user asked for illustrations - if so, generate them after streaming
            // Use the already-streamed text content instead of regenerating
            $input = $parameters['input'] ?? '';
            $hasIllustrationRequest = (bool) preg_match('/(illustrat|diagram|picture|image|drawing|sketch)/i', $input);

            if ($hasIllustrationRequest) {
                $result = $this->chatService->handleMixedGeneration($parameters, $conversationHistory, $result['content'] ?? '');
            }

            // Save the assistant message to the database
            AcademicChatMessage::create([
                'user_id'            => Auth::id(),
                'conversation_id'    => $this->conversationId,
                'conversation_title' => $this->conversationTitle,
                'content'            => $result['content'],
                'role'               => 'assistant',
                'parameters'         => $parameters,
                'usage'              => $result['usage'] ?? null,
                'model_used'         => $result['model_used'] ?? $result['model'] ?? null,
                'images'             => $result['images'] ?? null,
                'incomplete'         => $result['incomplete'] ?? false,
            ]);

            // Add the final message to the messages array
            $this->messages[] = [
                'role'       => 'assistant',
                'content'    => $result['content'],
                'timestamp'  => now()->toISOString(),
                'usage'      => $result['usage'] ?? null,
                'images'     => $result['images'] ?? null,
                'model_used' => $result['model_used'] ?? $result['model'] ?? null,
                'incomplete' => $result['incomplete'] ?? false,
            ];
        } else {
            // Handle error
            $error = $result['error'] ?? 'Unknown error';
            $this->errors[] = $error;

            AcademicChatMessage::create([
                'user_id'            => Auth::id(),
                'conversation_id'    => $this->conversationId,
                'conversation_title' => $this->conversationTitle,
                'content'            => '__error__: '.$error,
                'role'               => 'assistant',
                'parameters'         => $parameters,
            ]);
        }

        // Reset streaming state
        $this->isLoading = false;
        $this->pendingUserMessageId = null;
        $this->pendingMessageSentAt = null;
        $this->streamedAnswer = '';
        $this->isImageRequest = false;
        $this->loadConversationHistory();
        $this->dispatch('scroll-to-bottom');
    }

    public function checkForResponse(): void
    {
        if (! $this->isLoading || ! $this->pendingUserMessageId) {
            return;
        }

        // Safety timeout: if we've been waiting too long, give up and show an error
        if ($this->pendingMessageSentAt !== null &&
            (time() - $this->pendingMessageSentAt) > self::RESPONSE_TIMEOUT_SECONDS) {
            $this->errors[] = 'The request took too long to process. Please try again.';
            $this->isLoading = false;
            $this->pendingUserMessageId = null;
            $this->pendingMessageSentAt = null;
            $this->dispatch('scroll-to-bottom');
            return;
        }

        $userRow = AcademicChatMessage::find($this->pendingUserMessageId);
        if (! $userRow) {
            $this->isLoading = false;
            $this->pendingUserMessageId = null;
            $this->pendingMessageSentAt = null;
            return;
        }

        $assistantRow = AcademicChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $this->conversationId)
            ->where('role', 'assistant')
            ->where('created_at', '>', $userRow->created_at)
            ->latest('created_at')
            ->first();

        if (! $assistantRow) {
            return; // still waiting
        }

        $content = $this->normalizeStoredAssistantContent($assistantRow->content);

        $isError = str_starts_with($content, '__error__: ');
        if ($isError) {
            $this->errors[] = substr($content, strlen('__error__: '));
        } else {
            $this->messages[] = [
                'role'       => 'assistant',
                'content'    => $content,
                'timestamp'  => $assistantRow->created_at->toISOString(),
                'usage'      => $assistantRow->usage,
                'images'     => $assistantRow->images,
                'model_used' => $assistantRow->model_used,
                'incomplete' => $assistantRow->incomplete ?? false,
            ];
        }

        $this->isLoading = false;
        $this->pendingUserMessageId = null;
        $this->pendingMessageSentAt = null;
        $this->isImageRequest = false;
        $this->loadConversationHistory();
        $this->dispatch('scroll-to-bottom');
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
        $this->response_length = 4000;
    }

    /**
     * Convert markdown text to HTML for streaming display.
     * Uses a simple conversion to handle partial markdown chunks.
     */
    protected function markdownToHtml(string $text): string
    {
        try {
            $converter = new CommonMarkConverter([
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ]);
            return (string) $converter->convertToHtml($text);
        } catch (\Throwable $e) {
            // Fallback: escape and add basic line breaks
            return nl2br(e($text));
        }
    }

    public function render()
    {
        return view('livewire.research-assistant');
    }
}
