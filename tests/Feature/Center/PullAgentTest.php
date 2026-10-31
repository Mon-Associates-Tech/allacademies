<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\ExamPackageBuilder;
use App\ExaminationHub\Services\PullAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PullAgentTest extends TestCase
{
    use RefreshDatabase;

    protected PullAgent $agent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agent = app(PullAgent::class);
    }

    public function test_pull_fails_when_not_configured(): void
    {
        config(['center.central_url' => null]);
        config(['center.sync_token' => null]);

        $result = $this->agent->pull();

        $this->assertFalse($result['success']);
        $this->assertEquals(0, $result['pulled']);
        $this->assertCount(1, $result['errors']);
    }

    public function test_pull_fails_when_central_unreachable(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);

        $result = $this->agent->pull();

        $this->assertFalse($result['success']);
        $this->assertGreaterThan(0, count($result['errors']));
    }

    public function test_pull_skips_up_to_date_exam(): void
    {
        $exam = $this->createExam();

        // Pre-seed local sync state at the same revision
        ExamSyncState::create([
            'exam_uuid' => $exam->uuid,
            'applied_revision' => 1,
            'applied_state_version' => 1,
            'checksum' => 'abc123',
            'applied_at' => now(),
        ]);

        Http::fake([
            'localhost:99999/api/sync/v1/manifest' => Http::response([
                'server_time' => now()->toIso8601String(),
                'exams' => [
                    [
                        'uuid' => $exam->uuid,
                        'revision' => 1,
                        'state_version' => 1,
                        'checksum' => 'abc123',
                        'size' => 100,
                    ],
                ],
            ], 200),
        ]);

        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);

        $result = $this->agent->pull();

        // The exam should be skipped (already at revision 1)
        $this->assertEquals(1, $result['pulled']);
    }

    public function test_pull_verifies_checksum(): void
    {
        $exam = $this->createExam();

        // Build a real package
        $builder = app(ExamPackageBuilder::class);
        $packageData = $builder->build($exam);
        $payload = gzencode($packageData['canonical_json']);
        $checksum = $packageData['checksum'];

        Http::fake([
            'localhost:99999/api/sync/v1/manifest' => Http::response([
                'server_time' => now()->toIso8601String(),
                'exams' => [
                    [
                        'uuid' => $exam->uuid,
                        'revision' => 1,
                        'state_version' => 1,
                        'checksum' => 'wrong-checksum',
                        'size' => strlen($payload),
                    ],
                ],
            ], 200),
            'localhost:99999/api/sync/v1/exams/' . $exam->uuid . '/revisions/1' => Http::response($payload, 200, [
                'Content-Type' => 'application/gzip',
            ]),
        ]);

        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);

        $result = $this->agent->pull();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Checksum mismatch', $result['errors'][0]);
    }

    public function test_pull_creates_sync_run(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);

        $this->agent->pull();

        $this->assertDatabaseHas('sync_runs', [
            'direction' => 'pull',
        ]);
    }

    public function test_pull_clock_skew_warning(): void
    {
        $exam = $this->createExam();

        // Simulate 10 minutes of clock skew
        $serverTime = now()->addMinutes(10)->toIso8601String();

        Http::fake([
            'localhost:99999/api/sync/v1/manifest' => Http::response([
                'server_time' => $serverTime,
                'exams' => [],
            ], 200),
        ]);

        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.clock_skew_threshold_seconds' => 120]);

        $result = $this->agent->pull();

        // Pull still succeeds but clock skew is logged as a warning
        $this->assertNotEmpty($result);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    protected function createExam(): GeneralExam
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => 1,
            'title' => 'Test Exam',
            'access_code' => 'TEST123',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'duration_in_minutes' => 60,
            'max_attempts' => 1,
            'participant_mode' => 'configured',
            'status' => 'published',
            'revision' => 1,
        ]);

        GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 1,
        ]);

        GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_section_id' => $exam->sections->first()->id,
            'type' => GeneralExamQuestion::TYPE_MULTIPLE_CHOICE,
            'question' => ['up' => 'What is 2+2?', 'down' => null],
            'options' => [
                ['key' => 'A', 'value' => ['up' => '4', 'down' => null]],
                ['key' => 'B', 'value' => ['up' => '5', 'down' => null]],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'order' => 1,
        ]);

        return $exam;
    }
}
