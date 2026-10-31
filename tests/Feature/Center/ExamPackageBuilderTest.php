<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Services\ExamPackageBuilder;
use App\ExaminationHub\Services\ExamPublishService;
use App\ExaminationHub\Services\GradeScale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPackageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected ExamPackageBuilder $builder;
    protected ExamPublishService $publishService;

    protected function setUp(): void
    {
        parent::setUp();

        // Generate test keys for signing
        $keyPair = \sodium_crypto_sign_keypair();
        $publicKey = \sodium_crypto_sign_publickey($keyPair);
        $privateKey = \sodium_crypto_sign_secretkey($keyPair);

        config([
            'center.central_private_key' => base64_encode($privateKey),
            'center.central_public_key' => base64_encode($publicKey),
        ]);

        $this->builder = app(ExamPackageBuilder::class);
        $this->publishService = app(ExamPublishService::class);
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

        // Create a section
        $section = GeneralExamSection::create([
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 1,
            'is_randomized' => false,
        ]);

        // Create questions
        for ($i = 0; $i < 3; $i++) {
            GeneralExamQuestion::create([
                'general_exam_id' => $exam->id,
                'general_exam_section_id' => $section->id,
                'type' => 'multiple_choice',
                'question' => json_encode(['up' => 'Question ' . ($i + 1) . '?', 'down' => 'Question ' . ($i + 1) . '?']),
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

        // Create participants
        for ($i = 0; $i < 2; $i++) {
            GeneralExamConfiguredParticipant::create([
                'general_exam_id' => $exam->id,
                'name' => 'Candidate ' . ($i + 1),
                'email' => 'candidate' . ($i + 1) . '@test.com',
                'unique_code' => 'IDX' . ($i + 1),
                'is_active' => true,
            ]);
        }

        return $exam->refresh();
    }

    /** @test */
    public function it_builds_a_valid_package()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $this->assertArrayHasKey('exam_uuid', $package);
        $this->assertEquals($exam->uuid, $package['exam_uuid']);
        $this->assertEquals(1, $package['revision']);
        $this->assertEquals('TEST01', $package['access_code']);
        $this->assertArrayHasKey('settings', $package);
        $this->assertArrayHasKey('grading_scale', $package);
        $this->assertArrayHasKey('sections', $package);
        $this->assertArrayHasKey('participants', $package);
        $this->assertArrayHasKey('checksum', $package);
        $this->assertArrayHasKey('signature', $package);
    }

    /** @test */
    public function it_produces_byte_identical_packages()
    {
        $exam = $this->createValidExam();

        $package1 = $this->builder->build($exam);
        $package2 = $this->builder->build($exam);

        // The payload (without checksum/signature) should be identical
        $payload1 = $this->builder->toCanonicalJson(array_diff_key($package1, ['checksum' => '', 'signature' => '']));
        $payload2 = $this->builder->toCanonicalJson(array_diff_key($package2, ['checksum' => '', 'signature' => '']));

        $this->assertEquals($payload1, $payload2);
        $this->assertEquals($package1['checksum'], $package2['checksum']);
    }

    /** @test */
    public function it_includes_only_uuids_not_integer_ids()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        // Serialize the package to JSON and check for integer IDs
        $json = json_encode($package);

        // The exam's integer ID should not appear in the package
        $this->assertStringNotContainsString((string) $exam->id, $json);

        // UUIDs should be present
        $this->assertStringContainsString($exam->uuid, $json);
    }

    /** @test */
    public function it_includes_grading_scale()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $this->assertIsArray($package['grading_scale']);
        $this->assertNotEmpty($package['grading_scale']);

        // Default scale should be present
        $defaultScale = GradeScale::defaultArray();
        $this->assertEquals($defaultScale, $package['grading_scale']);
    }

    /** @test */
    public function it_includes_custom_grading_scale()
    {
        $customScale = [
            ['min' => 90, 'max' => 100, 'grade' => 'A'],
            ['min' => 60, 'max' => 89, 'grade' => 'B'],
            ['min' => 0, 'max' => 59, 'grade' => 'C'],
        ];

        $exam = $this->createValidExam(['grading_scale' => $customScale]);

        $package = $this->builder->build($exam);

        $this->assertEquals($customScale, $package['grading_scale']);
    }

    /** @test */
    public function it_includes_sections_with_questions()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $this->assertCount(1, $package['sections']);
        $section = $package['sections'][0];

        $this->assertArrayHasKey('uuid', $section);
        $this->assertArrayHasKey('questions', $section);
        $this->assertCount(3, $section['questions']);

        $question = $section['questions'][0];
        $this->assertArrayHasKey('uuid', $question);
        $this->assertArrayHasKey('type', $question);
        $this->assertArrayHasKey('marks', $question);
        $this->assertArrayHasKey('question', $question);
        $this->assertArrayHasKey('options', $question);
        $this->assertArrayHasKey('correct_answer', $question);
        $this->assertArrayHasKey('excluded_from_grading', $question);
        $this->assertArrayHasKey('award_marks_on_exclusion', $question);
    }

    /** @test */
    public function it_includes_participants()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $this->assertCount(2, $package['participants']);

        $participant = $package['participants'][0];
        $this->assertArrayHasKey('uuid', $participant);
        $this->assertArrayHasKey('index_number', $participant);
        $this->assertArrayHasKey('name', $participant);
        $this->assertArrayHasKey('email', $participant);
    }

    /** @test */
    public function it_includes_settings()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $settings = $package['settings'];
        $this->assertEquals('Mathematics Exam', $settings['title']);
        $this->assertEquals(60, $settings['duration_in_minutes']);
        $this->assertEquals(1, $settings['max_attempts']);
        $this->assertFalse($settings['is_randomized']);
        $this->assertEquals('configured', $settings['participant_mode']);
        $this->assertEquals('manual_release', $settings['result_visibility']);
    }

    /** @test */
    public function it_signs_the_package()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);

        $this->assertNotEmpty($package['checksum']);
        $this->assertNotEmpty($package['signature']);

        // Verify the signature
        $publicKeyBase64 = config('center.central_public_key');
        $publicKey = base64_decode($publicKeyBase64);
        $signature = base64_decode($package['signature']);

        $this->assertTrue(
            \sodium_crypto_sign_verify_detached($signature, $package['checksum'], $publicKey)
        );
    }

    /** @test */
    public function it_compresses_the_package()
    {
        $exam = $this->createValidExam();

        $package = $this->builder->build($exam);
        $compressed = $this->builder->compress($package);

        $this->assertIsString($compressed);
        $this->assertNotFalse($compressed);

        // Verify decompression
        $decompressed = gzdecode($compressed);
        $this->assertNotFalse($decompressed);

        $decoded = json_decode($decompressed, true);
        $this->assertIsArray($decoded);
        $this->assertEquals($package['exam_uuid'], $decoded['exam_uuid']);
    }

    /** @test */
    public function it_validates_missing_access_code()
    {
        $exam = $this->createValidExam(['access_code' => null]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('Exam must have an access code before publishing.', $errors);
    }

    /** @test */
    public function it_validates_missing_sections()
    {
        $user = User::factory()->create();
        $exam = GeneralExam::create([
            'title' => 'Empty Exam',
            'user_id' => $user->id,
            'school_id' => 1,
            'teacher_id' => 1,
            'type' => 'multiple_choice',
            'delivery_type' => 'online',
            'participant_mode' => 'open',
            'duration_in_minutes' => 60,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
            'max_attempts' => 1,
            'status' => 'draft',
            'access_code' => 'EMPTY1',
        ]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('Exam must have at least one section.', $errors);
    }

    /** @test */
    public function it_validates_missing_answer_key()
    {
        $exam = $this->createValidExam();

        // Remove answer key from one question
        $question = $exam->questions()->first();
        $question->update(['correct_answer' => null]);
        $exam->refresh()->load(['sections.questions']);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('is missing a correct answer key.', implode('', $errors));
    }

    /** @test */
    public function it_validates_unsupported_question_type()
    {
        $exam = $this->createValidExam();

        // Change one question to essay type
        $question = $exam->questions()->first();
        $question->update(['type' => 'essay']);
        $exam->refresh()->load(['sections.questions']);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('is not supported', implode('', $errors));
    }

    /** @test */
    public function it_validates_missing_start_time()
    {
        $exam = $this->createValidExam(['starts_at' => null]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('Exam must have a start time.', $errors);
    }

    /** @test */
    public function it_validates_missing_end_time()
    {
        $exam = $this->createValidExam(['ends_at' => null]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('Exam must have an end time.', $errors);
    }

    /** @test */
    public function it_validates_end_time_before_start_time()
    {
        $exam = $this->createValidExam([
            'starts_at' => now()->addHours(2),
            'ends_at' => now()->addHour(),
        ]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('Exam end time must be after start time.', $errors);
    }

    /** @test */
    public function it_validates_configured_mode_without_participants()
    {
        $user = User::factory()->create();
        $exam = GeneralExam::create([
            'title' => 'No Participants Exam',
            'user_id' => $user->id,
            'school_id' => 1,
            'teacher_id' => 1,
            'type' => 'multiple_choice',
            'delivery_type' => 'online',
            'participant_mode' => 'configured',
            'duration_in_minutes' => 60,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
            'max_attempts' => 1,
            'status' => 'draft',
            'access_code' => 'NOPART',
        ]);

        // Add a section with a question
        $section = GeneralExamSection::create([
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 1,
        ]);

        GeneralExamQuestion::create([
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => json_encode(['up' => 'Q1?', 'down' => 'Q1?']),
            'options' => [
                ['key' => 'A', 'value' => json_encode(['up' => 'A', 'down' => 'A'])],
                ['key' => 'B', 'value' => json_encode(['up' => 'B', 'down' => 'B'])],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'order' => 1,
        ]);

        $errors = $this->builder->validate($exam);

        $this->assertNotEmpty($errors);
        $this->assertContains('no participants', implode('', $errors));
    }

    /** @test */
    public function it_publishes_an_exam()
    {
        $exam = $this->createValidExam();

        $result = $this->publishService->publish($exam);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['package']);
        $this->assertEmpty($result['errors']);

        // Verify exam is updated
        $exam->refresh();
        $this->assertEquals('published', $exam->status);
        $this->assertEquals(1, $exam->revision);
        $this->assertNotNull($exam->published_at);
        $this->assertNotNull($exam->grading_scale);

        // Verify package is stored
        $this->assertDatabaseHas('general_exam_packages', [
            'exam_uuid' => $exam->uuid,
            'revision' => 1,
        ]);
    }

    /** @test */
    public function it_prevents_double_publish()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $result = $this->publishService->publish($exam);

        $this->assertFalse($result['success']);
        $this->assertContains('already published', implode('', $result['errors']));
    }

    /** @test */
    public function it_revises_a_published_exam()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        // Make a change - exclude a question
        $question = $exam->questions()->first();
        $question->update(['excluded_from_grading' => true]);
        $exam->refresh()->load(['sections.questions', 'configuredParticipants']);

        $result = $this->publishService->revise($exam);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['package']);

        // Verify revision 2 is created
        $exam->refresh();
        $this->assertEquals(2, $exam->revision);

        $this->assertDatabaseHas('general_exam_packages', [
            'exam_uuid' => $exam->uuid,
            'revision' => 2,
        ]);
    }

    /** @test */
    public function it_prevents_revision_of_unpublished_exam()
    {
        $exam = $this->createValidExam();

        $result = $this->publishService->revise($exam);

        $this->assertFalse($result['success']);
        $this->assertContains('not published', implode('', $result['errors']));
    }

    /** @test */
    public function it_releases_results()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $result = $this->publishService->releaseResults($exam);

        $this->assertTrue($result['success']);

        $exam->refresh();
        $this->assertTrue($exam->results_released);
        $this->assertNotNull($exam->results_released_at);
        $this->assertEquals(2, $exam->state_version);
    }

    /** @test */
    public function it_gets_state_block()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $stateBlock = $this->publishService->getStateBlock($exam);

        $this->assertEquals($exam->uuid, $stateBlock['exam_uuid']);
        $this->assertEquals(1, $stateBlock['state_version']);
        $this->assertEquals('published', $stateBlock['status']);
        $this->assertFalse($stateBlock['results_released']);
        $this->assertEquals(1, $stateBlock['revision']);
    }

    /** @test */
    public function it_verifies_package_signature()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = ExamPackage::latestForExam($exam->uuid);

        $this->assertTrue($package->verifyChecksum());
        $this->assertTrue($package->verifySignature(config('center.central_public_key')));
    }

    /** @test */
    public function it_detects_tampered_package()
    {
        $exam = $this->createValidExam();
        $this->publishService->publish($exam);

        $package = ExamPackage::latestForExam($exam->uuid);

        // Tamper with the payload
        $payload = $package->payload;
        $tamperedPayload = substr($payload, 0, -1) . (substr($payload, -1) === 'A' ? 'B' : 'A');
        $package->update(['payload' => $tamperedPayload]);

        $this->assertFalse($package->refresh()->verifyChecksum());
    }

    /** @test */
    public function it_formats_marks_correctly()
    {
        $exam = $this->createValidExam();

        // Set a question with decimal marks
        $question = $exam->questions()->first();
        $question->update(['marks' => 1.5]);
        $exam->refresh()->load(['sections.questions']);

        $package = $this->builder->build($exam);

        $q = $package['sections'][0]['questions'][0];
        $this->assertEquals('1.50', $q['marks']);
    }

    /** @test */
    public function it_handles_true_false_questions()
    {
        $exam = $this->createValidExam();

        // Add a True/False question
        $section = $exam->sections()->first();
        GeneralExamQuestion::create([
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'true_false',
            'question' => json_encode(['up' => 'True or False?', 'down' => 'True or False?']),
            'options' => [],
            'correct_answer' => 'true',
            'marks' => 1.0,
            'order' => 4,
        ]);

        $exam->refresh()->load(['sections.questions']);

        $errors = $this->builder->validate($exam);
        $this->assertEmpty($errors);

        $package = $this->builder->build($exam);
        $this->assertCount(4, $package['sections'][0]['questions']);
    }
}
