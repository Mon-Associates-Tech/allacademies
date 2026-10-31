<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\ExamPackageApplier;
use App\ExaminationHub\Services\ExamPackageBuilder;
use App\ExaminationHub\Services\ExamPublishService;
use App\ExaminationHub\Services\SyncManifestService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPackageApplierTest extends TestCase
{
    use RefreshDatabase;

    protected ExamPackageBuilder $builder;
    protected ExamPublishService $publishService;
    protected ExamPackageApplier $applier;

    protected function setUp(): void
    {
        parent::setUp();

        // Generate test keys
        $keyPair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keyPair);
        $privateKey = sodium_crypto_sign_secretkey($keyPair);

        config([
            'center.central_private_key' => base64_encode($privateKey),
            'center.central_public_key' => base64_encode($publicKey),
        ]);

        $this->builder = app(ExamPackageBuilder::class);
        $this->publishService = app(ExamPublishService::class);
        $this->applier = new ExamPackageApplier();
    }

    protected function createValidExam(array $overrides = []): GeneralExam
    {
        $user = User::factory()->create();

        $exam = GeneralExam::create(array_merge([
            'title' => 'Mathematics Exam',
            'description' => 'Test exam',
            'user_id' => $user->id,
            'school_id' => 1,
            'teacher_id' => 1,
            'type' => 'multiple_choice',
            'delivery_type' => 'online',
            'participant_mode' => 'configured',
            'participant_required_fields' => ['name', 'code'],
            'duration_in_minutes' => 60,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(3),
            'is_randomized' => false,
            'instructions' => 'Read each question carefully.',
            'total_marks' => 10,
            'result_visibility' => 'manual_release',
            'results_released' => false,
            'show_correct_answers' => true,
            'show_score_breakdown' => true,
            'proctoring_enabled' => false,
            'restrict_navigation' => false,
            'max_tab_switches' => 3,
            'auto_submit_on_violation' => false,
            'max_attempts' => 1,
            'status' => 'draft',
            'access_code' => 'TEST01',
            'revision' => 0,
            'state_version' => 1,
        ], $overrides));

        $section = GeneralExamSection::create([
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 1,
            'is_randomized' => false,
        ]);

        for ($i = 0; $i < 3; $i++) {
            GeneralExamQuestion::create([
                'general_exam_id' => $exam->id,
                'general_exam_section_id' => $section->id,
                'type' => 'multiple_choice',
                'question' => json_encode(['up' => "Question {$i + 1}?", 'down' => "Question {$i + 1}?"]),
                'options' => [
                    ['key' => 'A', 'value' => json_encode(['up' => 'Option A', 'down' => 'Option A'])],
                    ['key' => 'B', 'value' => json_encode(['up' => 'Option B', 'down' => 'Option B'])],
                    ['key' => 'C', 'value' => json_encode(['up' => 'Option C', 'down' => 'Option C'])],
                    ['key' => 'D', 'value' => json_encode(['up' => 'Option D', 'down' => 'Option D'])],
                ],
                'correct_answer' => 'B',
                'marks' => 1.0,
                'order' => $i + 1,
                'excluded_from_grading' => false,
                'award_marks_on_exclusion' => false,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            GeneralExamConfiguredParticipant::create([
                'general_exam_id' => $exam->id,
                'name' => "Candidate {$i + 1}",
                'email' => "candidate{$i + 1}@test.com",
                'unique_code' => "IDX{$i + 1}",
                'is_active' => true,
            ]);
        }

        return $exam->refresh();
    }

    /** @test */
    public function it_applies_a_new_package()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = $this->builder->build($exam);

        $result = $this->applier->apply($package);

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);

        // Verify exam exists
        $this->assertDatabaseHas('general_exams', [
            'uuid' => $exam->uuid,
            'access_code' => 'TEST01',
            'status' => 'published',
        ]);

        // Verify sections exist
        $this->assertEquals(1, GeneralExamSection::where('general_exam_id', $exam->id)->count());

        // Verify questions exist
        $this->assertEquals(3, GeneralExamQuestion::where('general_exam_id', $exam->id)->count());

        // Verify participants exist
        $this->assertEquals(2, GeneralExamConfiguredParticipant::where('general_exam_id', $exam->id)->count());
    }

    /** @test */
    public function it_is_idempotent()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = $this->builder->build($exam);

        // Apply twice
        $this->applier->apply($package);
        $result = $this->applier->apply($package);

        $this->assertTrue($result['success']);

        // No duplicates
        $this->assertEquals(1, GeneralExam::where('uuid', $exam->uuid)->count());
        $this->assertEquals(3, GeneralExamQuestion::where('general_exam_id', $exam->id)->count());
        $this->assertEquals(2, GeneralExamConfiguredParticipant::where('general_exam_id', $exam->id)->count());
    }

    /** @test */
    public function it_applies_revision()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        // Exclude a question
        $question = $exam->questions()->first();
        $question->update(['excluded_from_grading' => true]);
        $exam->refresh()->load(['sections.questions', 'configuredParticipants']);

        $this->publishService->revise($exam);

        $package = $this->builder->build($exam);
        $result = $this->applier->apply($package);

        $this->assertTrue($result['success']);

        $exam->refresh();
        $this->assertEquals(2, $exam->revision);
    }

    /** @test */
    public function it_preserves_access_code()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = $this->builder->build($exam);

        $this->applier->apply($package);

        $localExam = GeneralExam::findByUuid($exam->uuid);
        $this->assertEquals('TEST01', $localExam->access_code);
    }

    /** @test */
    public function it_sets_null_user_id_on_center()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = $this->builder->build($exam);

        $this->applier->apply($package);

        $localExam = GeneralExam::findByUuid($exam->uuid);
        $this->assertNull($localExam->user_id);
        $this->assertNull($localExam->school_id);
        $this->assertNull($localExam->teacher_id);
    }
}
