<?php

namespace Tests\Unit;

use App\Services\AcademicChatService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AcademicChatServiceTest extends TestCase
{
    protected AcademicChatService $chatService;

    /** @test */
    public function it_returns_correct_available_subjects()
    {
        $subjects = $this->chatService->getAvailableSubjects();

        $this->assertIsArray($subjects);
        $this->assertArrayHasKey('mathematics', $subjects);
        $this->assertArrayHasKey('science', $subjects);
        $this->assertArrayHasKey('language_arts', $subjects);

// Check that mathematics has expected topics
        $this->assertContains('algebra', $subjects['mathematics']);
        $this->assertContains('geometry', $subjects['mathematics']);
    }

    /** @test */
    public function parameter_validation_catches_all_error_types()
    {
        $testCases = [
// Missing message
            ['age' => 15] => 'Message is required',

// Invalid age
            ['message' => 'test', 'age' => 3] => 'Age must be between 5 and 100',
            ['message' => 'test', 'age' => 150] => 'Age must be between 5 and 100',

// Invalid academic level
            ['message' => 'test', 'academic_level' => 'invalid'] => 'Invalid academic level',

// Invalid learning style
            ['message' => 'test', 'learning_style' => 'invalid'] => 'Invalid learning style',

// Invalid creativity level
            ['message' => 'test', 'creativity_level' => 2] => 'Creativity level must be between 0 and 1',
        ];

        foreach ($testCases as $params => $expectedError) {
            $errors = $this->chatService->validateParameters($params);
            $this->assertContains($expectedError, $errors, "Failed for parameters: " . json_encode($params));
        }
    }

    /** @test */
    public function system_message_adapts_to_age_groups()
    {
        $reflection = new ReflectionClass($this->chatService);
        $method = $reflection->getMethod('buildAcademicSystemMessage');
        $method->setAccessible(true);

// Test elementary age
        $elementaryMessage = $method->invoke($this->chatService, ['age' => 8]);
        $this->assertStringContainsString('simple, friendly language', $elementaryMessage);

// Test high school age
        $highSchoolMessage = $method->invoke($this->chatService, ['age' => 16]);
        $this->assertStringContainsString('comprehensive language', $highSchoolMessage);

// Test college age
        $collegeMessage = $method->invoke($this->chatService, ['age' => 20]);
        $this->assertStringContainsString('academic language', $collegeMessage);
    }

    /** @test */
    public function system_message_includes_learning_style_adaptations()
    {
        $reflection = new ReflectionClass($this->chatService);
        $method = $reflection->getMethod('buildAcademicSystemMessage');
        $method->setAccessible(true);

        $visualMessage = $method->invoke($this->chatService, ['learning_style' => 'visual']);
        $this->assertStringContainsString('visual descriptions', $visualMessage);

        $auditoryMessage = $method->invoke($this->chatService, ['learning_style' => 'auditory']);
        $this->assertStringContainsString('verbal descriptions', $auditoryMessage);

        $kinestheticMessage = $method->invoke($this->chatService, ['learning_style' => 'kinesthetic']);
        $this->assertStringContainsString('hands-on activities', $kinestheticMessage);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatService = new AcademicChatService();
    }
}

