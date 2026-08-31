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

    public function handle(ResearchAssistantService $service): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        // Temporarily bind the user so service token checks work
        auth()->setUser($user);

        try {
            $response = $service->processRequest($this->parameters, $this->conversationHistory);

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
            Log::error('ProcessResearchAssistantMessageJob failed', [
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
