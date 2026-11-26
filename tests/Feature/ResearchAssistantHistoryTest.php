<?php

namespace Tests\Feature;

use App\Livewire\ResearchAssistant;
use ReflectionClass;
use Tests\TestCase;

class ResearchAssistantHistoryTest extends TestCase
{
    /** @test */
    public function it_excludes_the_pending_user_message_from_conversation_history(): void
    {
        $component = new ResearchAssistant;
        $component->messages = [
            ['role' => 'user', 'content' => 'Earlier question'],
            ['role' => 'assistant', 'content' => 'Earlier answer'],
            ['role' => 'user', 'content' => 'Current question'],
        ];
        $component->pendingUserMessageId = 123;

        $method = (new ReflectionClass($component))->getMethod('getConversationHistory');
        $history = $method->invoke($component);

        $this->assertSame([
            ['role' => 'user', 'content' => 'Earlier question'],
            ['role' => 'assistant', 'content' => 'Earlier answer'],
        ], $history);
    }
}
