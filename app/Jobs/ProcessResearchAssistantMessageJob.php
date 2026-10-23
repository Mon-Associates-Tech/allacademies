<?php

namespace App\Jobs;

use App\Models\AcademicChatMessage;
use App\Models\User;
use App\Services\ResearchAssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessResearchAssistantMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(
        public readonly int    $userMessageId,
        public readonly int    $userId,
        public readonly string $conversationId,
        public readonly string $conversationTitle,
        public readonly array  $parameters,
        public readonly array  $conversationHistory,
    ) {}

    /**
     * Safe log wrapper — if the log file is unwritable (e.g. permission mismatch
     * between the queue worker user and the web server user), the job continues
     * instead of crashing with an uncaught StreamHandler exception.
     */
    protected function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::$level($message, $context);
        } catch (\Throwable $e) {
            // Logging failed (e.g. permission denied on log file).
            // Silently ignore — the job still stores results in the database.
        }
    }

    public function handle(ResearchAssistantService $service): void
    {
        $this->safeLog('info', 'ProcessResearchAssistantMessageJob started', [
            'user_message_id' => $this->userMessageId,
            'user_id' => $this->userId,
            'conversation_id' => $this->conversationId,
        ]);

        $user = User::find($this->userId);

        if (! $user) {
            $this->safeLog('warning', 'ProcessResearchAssistantMessageJob: user not found', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        // Temporarily bind the user so service token checks work
        auth()->setUser($user);

        try {
            $this->safeLog('info', 'ProcessResearchAssistantMessageJob: calling processRequest', [
                'user_message_id' => $this->userMessageId,
            ]);

            $response = $service->processRequest($this->parameters, $this->conversationHistory);

            $this->safeLog('info', 'ProcessResearchAssistantMessageJob: processRequest completed', [
                'user_message_id' => $this->userMessageId,
                'success' => $response['success'] ?? false,
                'has_error' => isset($response['error']),
                'error_message' => $response['error'] ?? null,
                'content_length' => isset($response['content']) ? strlen($response['content']) : null,
                'content_preview' => isset($response['content']) ? substr($response['content'], 0, 200) : null,
                'has_usage' => isset($response['usage']),
            ]);

            if ($response['success']) {
                $content = $response['content'];

                AcademicChatMessage::create([
                    'user_id'            => $this->userId,
                    'conversation_id'    => $this->conversationId,
                    'conversation_title' => $this->conversationTitle,
                    'content'            => $content,
                    'role'               => 'assistant',
                    'parameters'         => $this->parameters,
                    'usage'              => $response['usage'] ?? null,
                    'model_used'         => $response['model_used'] ?? $response['model'] ?? null,
                    'images'             => $response['images'] ?? null,
                ]);
            } else {
                // Store the error as an assistant message so the UI can display it
                AcademicChatMessage::create([
                    'user_id'            => $this->userId,
                    'conversation_id'    => $this->conversationId,
                    'conversation_title' => $this->conversationTitle,
                    'content'            => '__error__: ' . ($response['error'] ?? 'Unknown error'),
                    'role'               => 'assistant',
                    'parameters'         => $this->parameters,
                ]);
            }
        } catch (\Throwable $e) {
            $this->safeLog('error', 'ProcessResearchAssistantMessageJob failed', [
                'user_message_id' => $this->userMessageId,
                'error'           => $e->getMessage(),
            ]);

            AcademicChatMessage::create([
                'user_id'            => $this->userId,
                'conversation_id'    => $this->conversationId,
                'conversation_title' => $this->conversationTitle,
                'content'            => '__error__: Service temporarily unavailable. Please try again.',
                'role'               => 'assistant',
                'parameters'         => $this->parameters,
            ]);
        }
    }
}
