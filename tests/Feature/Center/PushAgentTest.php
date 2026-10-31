<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\PushAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PushAgentTest extends TestCase
{
    use RefreshDatabase;

    protected PushAgent $agent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agent = app(PushAgent::class);
    }

    public function test_push_fails_when_not_configured(): void
    {
        config(['center.central_url' => null]);
        config(['center.sync_token' => null]);
        config(['center.center_id' => null]);

        $result = $this->agent->push();

        $this->assertArrayHasKey('error', $result['submissions']);
        $this->assertArrayHasKey('error', $result['proctoring']);
    }

    public function test_push_creates_sync_run(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.center_id' => 1]);

        // No pending data, so push should succeed with 0 items
        Http::fake();

        $this->agent->push();

        $this->assertDatabaseHas('sync_runs', [
            'direction' => 'push',
        ]);
    }

    public function test_push_sends_submissions_with_answers(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.center_id' => 1]);

        $exam = $this->createExamWithQuestion();
        $submission = $this->createSubmission($exam);
        $question = $exam->questions->first();

        SubmissionAnswer::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'question_uuid' => $question->uuid,
            'response' => 'A',
            'answered_at' => now(),
        ]);

        // Ensure the submission is pending (synced_version < sync_version)
        $submission->update([
            'sync_version' => 2,
            'synced_version' => 1,
        ]);

        Http::fake([
            'localhost:99999/api/sync/v1/ingest/submissions' => Http::response([
                'total' => 1,
                'ingested' => 1,
                'skipped' => 0,
                'errors' => [],
            ], 200),
            'localhost:99999/api/sync/v1/ingest/proctoring' => Http::response([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ], 200),
        ]);

        $result = $this->agent->push();

        $this->assertEquals(1, $result['submissions']['ingested']);
        // Submission should now be synced
        $this->assertEquals(2, $submission->fresh()->synced_version);
    }

    public function test_push_skips_already_synced_submissions(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.center_id' => 1]);

        $exam = $this->createExamWithQuestion();
        $submission = $this->createSubmission($exam);

        // Already synced
        $submission->update([
            'sync_version' => 1,
            'synced_version' => 1,
        ]);

        Http::fake([
            'localhost:99999/api/sync/v1/ingest/submissions' => Http::response([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ], 200),
            'localhost:99999/api/sync/v1/ingest/proctoring' => Http::response([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ], 200),
        ]);

        $result = $this->agent->push();

        // No submissions should be pushed since all are synced
        $this->assertEquals(0, $result['submissions']['ingested']);
    }

    public function test_push_sends_proctoring_logs(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.center_id' => 1]);

        $exam = $this->createExamWithQuestion();
        $submission = $this->createSubmission($exam);

        ExamProctoringLog::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_submission_id' => $submission->id,
            'event_type' => 'tab_switch',
            'event_data' => ['count' => 1],
            'severity' => 'warning',
            'occurred_at' => now(),
            'center_id' => 1,
            'synced_at' => null,
        ]);

        Http::fake([
            'localhost:99999/api/sync/v1/ingest/submissions' => Http::response([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ], 200),
            'localhost:99999/api/sync/v1/ingest/proctoring' => Http::response([
                'total' => 1,
                'ingested' => 1,
                'skipped' => 0,
                'errors' => [],
            ], 200),
        ]);

        $result = $this->agent->push();

        $this->assertEquals(1, $result['proctoring']['ingested']);
        // Log should now be synced
        $this->assertNotNull(ExamProctoringLog::first()->fresh()->synced_at);
    }

    public function test_push_handles_failed_response(): void
    {
        config(['center.central_url' => 'http://localhost:99999']);
        config(['center.sync_token' => 'test-token']);
        config(['center.center_id' => 1]);

        $exam = $this->createExamWithQuestion();
        $submission = $this->createSubmission($exam);
        $submission->update([
            'sync_version' => 2,
            'synced_version' => 1,
        ]);

        Http::fake([
            'localhost:99999/api/sync/v1/ingest/submissions' => Http::response(
                ['error' => 'Internal server error'],
                500
            ),
            'localhost:99999/api/sync/v1/ingest/proctoring' => Http::response([
                'total' => 0,
                'ingested' => 0,
                'skipped' => 0,
                'errors' => [],
            ], 200),
        ]);

        $result = $this->agent->push();

        // Should have errors
        $this->assertGreaterThan(0, $result['submissions']['errors']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    protected function createExamWithQuestion(): GeneralExam
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

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 1,
        ]);

        GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_section_id' => $section->id,
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

    protected function createSubmission(GeneralExam $exam): GeneralExamSubmission
    {
        return GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_name' => 'Test Candidate',
            'participant_index_number' => 'IND001',
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'score' => 1.0,
            'total_marks' => 1.0,
            'percentage' => 100.0,
            'grade' => 'A',
            'sync_version' => 1,
            'synced_version' => 0,
        ]);
    }
}
