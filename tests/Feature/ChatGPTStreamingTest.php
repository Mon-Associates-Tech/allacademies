<?php

namespace Tests\Feature;

use App\Services\ChatGPTService;
use GuzzleHttp\Psr7\PumpStream;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatGPTStreamingTest extends TestCase
{
    /** @test */
    public function it_delivers_text_deltas_before_the_response_stream_is_consumed(): void
    {
        config(['services.openai.key' => 'test-key']);

        $deltaEvent = "event: response.output_text.delta\ndata: {\"type\":\"response.output_text.delta\",\"delta\":\"Hello\"}\n\n";
        $stream = new PumpStream(function () use ($deltaEvent) {
            static $sent = false;

            if ($sent) {
                return false;
            }

            $sent = true;

            return $deltaEvent.str_repeat(' ', 2048);
        });

        Http::fake(function ($request, array $options) use ($stream) {
            $this->assertTrue($options['stream'] ?? false);

            return Http::response($stream);
        });

        $receivedChunks = [];
        $result = app(ChatGPTService::class)->streamChat(
            [
                'model' => 'gpt-6.2-sol',
                'input' => [['role' => 'user', 'content' => 'Say hello']],
            ],
            function (string $chunk) use (&$receivedChunks, $stream): void {
                $this->assertFalse($stream->eof(), 'The text callback should run before the response reaches EOF.');
                $receivedChunks[] = $chunk;
            }
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Hello', $result['content']);
        $this->assertSame(['Hello'], $receivedChunks);
    }
}
