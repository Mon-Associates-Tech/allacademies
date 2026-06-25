<?php

namespace Tests\Feature\ExaminationHub;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Services\ExamParticipantAccessService;

class ExamParticipantAccessServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function configured_mode_requires_exact_match_of_both_email_and_code(): void
    {
        // Create exam in configured mode
        $exam = GeneralExam::factory()->create([
            'participant_mode' => 'configured',
            'configured_match_mode' => 'both',
        ]);

        // Create a configured participant
        GeneralExamConfiguredParticipant::factory()->create([
            'general_exam_id' => $exam->id,
            'email' => 'test@example.com',
            'unique_code' => 'TEST123',
            'is_active' => true,
        ]);

        // Test with correct email and code - should allow access
        $service = app(ExamParticipantAccessService::class);
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', 'TEST123');
        $this->assertTrue($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
        
        // Test with correct email but wrong code - should deny access
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', 'WRONGCODE');
        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
        
        // Test with wrong email but correct code - should deny access
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'wrong@example.com', 'TEST123');
        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);

        // Test with correct email but no code - should deny access
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', null);
        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
        
        // Test with neither email nor code - should deny access
        $result = $service->authorizeJoinByCode($exam, 'Test User', '', null);
        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
    }

    /** @test */
    public function configured_mode_with_any_match_still_denies_people_not_on_the_list(): void
    {
        $exam = GeneralExam::factory()->create([
            'participant_mode' => 'configured',
            'configured_match_mode' => 'any',
        ]);

        GeneralExamConfiguredParticipant::factory()->create([
            'general_exam_id' => $exam->id,
            'email' => 'listed@example.com',
            'unique_code' => 'LISTED123',
            'is_active' => true,
        ]);

        $service = app(ExamParticipantAccessService::class);

        $result = $service->authorizeJoinByCode($exam, 'Unlisted User', 'unlisted@example.com', 'WRONG123');

        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);

        $result = $service->authorizeJoinByCode($exam, 'Unlisted User', 'unlisted@example.com', 'LISTED123');

        $this->assertFalse($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
    }

    /** @test */
    public function general_mode_allows_anyone_to_join(): void
    {
        // Create exam in general mode
        $exam = GeneralExam::factory()->create([
            'participant_mode' => 'general',
        ]);

        // Test with any email and no code - should allow access
        $service = app(ExamParticipantAccessService::class);
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', null);
        $this->assertTrue($result['allowed']);
        $this->assertEquals('general', $result['mode']);
        
        // Test with no email and no code - should allow access
        $result = $service->authorizeJoinByCode($exam, 'Test User', '', null);
        $this->assertTrue($result['allowed']);
        $this->assertEquals('general', $result['mode']);
    }

    /** @test */
    public function both_mode_allows_either_configured_or_general(): void
    {
        // Create exam in both mode
        $exam = GeneralExam::factory()->create([
            'participant_mode' => 'both',
            'configured_match_mode' => 'any',
        ]);

        // Create a configured participant
        GeneralExamConfiguredParticipant::factory()->create([
            'general_exam_id' => $exam->id,
            'email' => 'test@example.com',
            'unique_code' => 'TEST123',
            'is_active' => true,
        ]);

        // Test with correct email and code - should allow access as configured
        $service = app(ExamParticipantAccessService::class);
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', 'TEST123');
        $this->assertTrue($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
        
        // Test with correct email but wrong code - should allow access as general
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'test@example.com', 'WRONGCODE');
        $this->assertTrue($result['allowed']);
        $this->assertEquals('configured', $result['mode']);
        
        // Test with wrong email and wrong code - should allow access as general
        $result = $service->authorizeJoinByCode($exam, 'Test User', 'wrong@example.com', 'WRONGCODE');
        $this->assertTrue($result['allowed']);
        $this->assertEquals('general', $result['mode']);
    }
}
