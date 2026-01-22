<?php

namespace Tests\Feature;

use App\Livewire\AcademicChat;
use App\Models\AcademicChatSession;
use App\Models\User;
use App\Services\AcademicChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AcademicChatTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicChatService $chatService;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.openai.key', 'test-key');
        $this->chatService = new AcademicChatService;
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_validate_educational_parameters()
    {
        $validParameters = [
            'message' => 'Test message',
            'age' => 15,
            'academic_level' => 'high_school',
            'subject' => 'mathematics',
            'learning_style' => 'visual',
        ];

        $errors = $this->chatService->validateParameters($validParameters);
        $this->assertEmpty($errors);

        $invalidParameters = [
            'message' => '', // Empty message
            'age' => 200, // Invalid age
            'academic_level' => 'invalid_level',
            'learning_style' => 'invalid_style',
        ];

        $errors = $this->chatService->validateParameters($invalidParameters);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function it_builds_appropriate_system_messages()
    {
        $parameters = [
            'age' => 10,
            'academic_level' => 'elementary',
            'subject' => 'mathematics',
            'learning_style' => 'visual',
            'accommodations' => ['simplified_language'],
        ];

        $reflection = new \ReflectionClass($this->chatService);
        $method = $reflection->getMethod('buildAcademicSystemMessage');
        $method->setAccessible(true);

        $systemMessage = $method->invoke($this->chatService, $parameters);

        $this->assertStringContainsString('elementary', $systemMessage);
        $this->assertStringContainsString('mathematics', $systemMessage);
        $this->assertStringContainsString('visual', $systemMessage);
        $this->assertStringContainsString('simplified', $systemMessage);
    }

    /** @test */
    public function it_can_handle_successful_api_responses()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a test response about mathematics.',
                        ],
                    ],
                ],
                'usage' => [
                    'total_tokens' => 100,
                ],
                'model' => 'gpt-4',
            ], 200),
        ]);

        $parameters = [
            'message' => 'Explain addition',
            'age' => 8,
            'subject' => 'mathematics',
        ];

        $result = $this->chatService->chat($parameters);

        $this->assertTrue($result['success']);
        $this->assertEquals('This is a test response about mathematics.', $result['content']);
        $this->assertArrayHasKey('usage', $result);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'error' => [
                    'message' => 'API Error',
                    'type' => 'invalid_request_error',
                ],
            ], 400),
        ]);

        $parameters = [
            'message' => 'Test message',
            'age' => 15,
        ];

        $result = $this->chatService->chat($parameters);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function livewire_component_can_send_messages()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Great question about mathematics!',
                        ],
                    ],
                ],
                'usage' => ['total_tokens' => 50],
                'model' => 'gpt-4',
            ], 200),
        ]);

        Livewire::test(AcademicChat::class)
            ->set('message', 'What is algebra?')
            ->set('age', 15)
            ->set('subject', 'mathematics')
            ->call('sendMessage')
            ->assertSet('message', '') // Message should be cleared
            ->assertCount('messages', 2); // User message + AI response
    }

    /** @test */
    public function livewire_component_validates_input()
    {
        Livewire::test(AcademicChat::class)
            ->set('message', '') // Empty message
            ->call('sendMessage')
            ->assertHasErrors(['message']);

        Livewire::test(AcademicChat::class)
            ->set('age', 200) // Invalid age
            ->call('sendMessage')
            ->assertHasErrors(['age']);
    }

    /** @test */
    public function it_can_clear_chat_history()
    {
        Livewire::test(AcademicChat::class)
            ->set('messages', [
                ['role' => 'user', 'content' => 'Test message'],
            ])
            ->call('clearChat')
            ->assertSet('messages', []);
    }

    /** @test */
    public function it_updates_available_topics_when_subject_changes()
    {
        Livewire::test(AcademicChat::class)
            ->set('subject', 'mathematics')
            ->assertSet('availableTopics', ['algebra', 'geometry', 'calculus', 'statistics', 'trigonometry']);
    }

    /** @test */
    public function api_endpoint_requires_valid_parameters()
    {
        $response = $this->postJson('/api/v1/educational-chat/chat', [
            'message' => '', // Invalid
            'age' => 200, // Invalid
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function api_endpoint_returns_successful_response()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Academic response about science.',
                        ],
                    ],
                ],
                'usage' => ['total_tokens' => 75],
                'model' => 'gpt-4',
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/educational-chat/chat', [
            'message' => 'Explain photosynthesis',
            'age' => 12,
            'subject' => 'science',
            'academic_level' => 'middle_school',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'content' => 'Academic response about science.',
            ]);
    }

    /** @test */
    public function it_returns_available_subjects()
    {
        $response = $this->getJson('/api/v1/educational-chat/subjects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'subjects' => [
                    'mathematics' => [],
                    'science' => [],
                    'language_arts' => [],
                ],
            ]);
    }

    /** @test */
    public function it_provides_learning_recommendations()
    {
        $response = $this->postJson('/api/v1/educational-chat/recommendations', [
            'age' => 14,
            'academic_level' => 'middle_school',
            'learning_style' => 'visual',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'recommendations' => [],
            ]);
    }

    /** @test */
    public function rate_limiting_works_correctly()
    {
        // This would test the rate limiting middleware
        // Mock high number of requests
        for ($i = 0; $i < 55; $i++) {
            $response = $this->postJson('/api/v1/educational-chat/chat', [
                'message' => "Test message {$i}",
                'age' => 15,
            ]);

            if ($i < 50) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
                break;
            }
        }
    }

    /** @test */
    public function chat_session_model_works_correctly()
    {
        $session = AcademicChatSession::create([
            'session_id' => 'test-session',
            'user_id' => $this->user->id,
            'parameters' => ['age' => 15, 'subject' => 'math'],
            'messages' => [],
            'last_activity' => now(),
        ]);

        $session->addMessage([
            'role' => 'user',
            'content' => 'Test message',
        ]);

        $this->assertCount(1, $session->fresh()->messages);

        $stats = $session->getStats();
        $this->assertEquals(1, $stats['total_messages']);
        $this->assertEquals(1, $stats['user_messages']);
    }
}
