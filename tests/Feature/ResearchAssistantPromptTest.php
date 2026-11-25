<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChatGPTService;
use App\Services\ModelSelectionService;
use App\Services\ResearchAssistantService;
use App\Services\TokenUsageService;
use Mockery;
use Tests\TestCase;

class ResearchAssistantPromptTest extends TestCase
{
    /** @test */
    public function it_preserves_the_typed_message_and_guides_the_model_to_answer_it_directly(): void
    {
        $user = Mockery::mock(User::class);
        $this->actingAs($user);

        $requestData = null;
        $chatGPTService = Mockery::mock(ChatGPTService::class);
        $chatGPTService->shouldReceive('streamChat')
            ->once()
            ->withArgs(function (array $data, callable $callback) use (&$requestData): bool {
                $requestData = $data;

                return true;
            })
            ->andReturn(['success' => true, 'content' => 'Hello! How can I help?']);

        $modelSelectionService = Mockery::mock(ModelSelectionService::class);
        $modelSelectionService->shouldReceive('getTextModelForUser')
            ->once()
            ->with($user)
            ->andReturn('test-model');

        $service = new ResearchAssistantService(
            $chatGPTService,
            $modelSelectionService,
            app(TokenUsageService::class)
        );

        $service->streamTextGeneration(
            ['input' => 'hello'],
            [],
            static function (string $chunk): void {}
        );

        $messages = $requestData['input'];
        $this->assertSame('User request: hello', end($messages)['content']);
        $this->assertStringContainsString('Answer the current user request directly', $messages[0]['content']);
        $this->assertStringContainsString('never invent or change a number', $messages[0]['content']);
    }
}
