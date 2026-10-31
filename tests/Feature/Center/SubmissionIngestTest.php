<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\ExamPackage;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Services\SubmissionIngestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionIngestTest extends TestCase
{
    use RefreshDatabase;

    protected SubmissionIngestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SubmissionIngestService::class);
    }

    public function test_ingest_creates_new_submission(): void
    {
        $exam = $this->createExam();
        $centerId = 1;

        $batch = [
            [
                'uuid' => (string) \Illuminate\Support\Str::ulid(),
                'exam_uuid' => $exam->uuid,
                'participant_name' => 'Test Candidate',
                'participant_index_number' => 'IND001',
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'score' => 8.5,
                'total_marks' => 10.0,
                'percentage' => 85.0,
                'grade' => 'A',
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        $result = $this->service->ingest($batch, $centerId);

        $this->assertEquals(1, $result['ingested']);
        $this->assertEquals(0, $result['skipped']);
        $this->assertCount(0, $result['errors']);

        $submissionUuid = $batch[0]['uuid'];
        $this->assertDatabaseHas('general_exam_submissions', [
            'uuid' => $submissionUuid,
            'center_id' => $centerId,
            'score' => 8.5,
            'synced_version' => 1,
        ]);
    }

    public function test_ingest_is_idempotent(): void
    {
        $exam = $this->createExam();
        $centerId = 1;

        $uuid = (string) \Illuminate\Support\Str::ulid();
        $batch = [
            [
                'uuid' => $uuid,
                'exam_uuid' => $exam->uuid,
                'participant_name' => 'Test Candidate',
                'participant_index_number' => 'IND001',
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'score' => 8.5,
                'total_marks' => 10.0,
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        // First ingest
        $result1 = $this->service->ingest($batch, $centerId);
        $this->assertEquals(1, $result1['ingested']);

        // Second ingest with same sync_version should be skipped
        $result2 = $this->service->ingest($batch, $centerId);
        $this->assertEquals(1, $result2['skipped']);
        $this->assertEquals(0, $result2['ingested']);

        // Only one submission should exist
        $this->assertEquals(1, GeneralExamSubmission::where('uuid', $uuid)->count());
    }

    public function test_ingest_accepts_higher_sync_version(): void
    {
        $exam = $this->createExam();
        $centerId = 1;

        $uuid = (string) \Illuminate\Support\Str::ulid();
        $batch = [
            [
                'uuid' => $uuid,
                'exam_uuid' => $exam->uuid,
                'participant_name' => 'Test Candidate',
                'participant_index_number' => 'IND001',
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'score' => 8.5,
                'total_marks' => 10.0,
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        // First ingest
        $this->service->ingest($batch, $centerId);

        // Update with higher sync_version
        $batch[0]['sync_version'] = 2;
        $batch[0]['score'] = 9.0;
        $result = $this->service->ingest($batch, $centerId);

        $this->assertEquals(1, $result['ingested']);
        $this->assertDatabaseHas('general_exam_submissions', [
            'uuid' => $uuid,
            'score' => 9.0,
            'synced_version' => 2,
        ]);
    }

    public function test_ingest_rejects_unknown_exam(): void
    {
        $batch = [
            [
                'uuid' => (string) \Illuminate\Support\Str::ulid(),
                'exam_uuid' => '01NONEXISTENTEXAM',
                'participant_name' => 'Test Candidate',
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        $result = $this->service->ingest($batch, 1);

        $this->assertEquals(0, $result['ingested']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('Unknown exam_uuid', $result['errors'][0]['error']);
    }

    public function test_ingest_creates_answers(): void
    {
        $exam = $this->createExamWithQuestion();
        $centerId = 1;

        $question = $exam->questions->first();
        $submissionUuid = (string) \Illuminate\Support\Str::ulid();
        $answerUuid = (string) \Illuminate\Support\Str::ulid();

        $batch = [
            [
                'uuid' => $submissionUuid,
                'exam_uuid' => $exam->uuid,
                'participant_name' => 'Test Candidate',
                'participant_index_number' => 'IND001',
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'score' => 1.0,
                'total_marks' => 1.0,
                'sync_version' => 1,
                'answers' => [
                    [
                        'uuid' => $answerUuid,
                        'question_uuid' => $question->uuid,
                        'response' => 'A',
                        'answered_at' => now()->toIso8601String(),
                    ],
                ],
            ],
        ];

        $result = $this->service->ingest($batch, $centerId);

        $this->assertEquals(1, $result['ingested']);
        $this->assertDatabaseHas('submission_answers', [
            'uuid' => $answerUuid,
            'response' => 'A',
        ]);
    }

    public function test_ingest_upserts_participant_by_index_number(): void
    {
        $exam = $this->createExam();
        $centerId = 1;

        $batch = [
            [
                'uuid' => (string) \Illuminate\Support\Str::ulid(),
                'exam_uuid' => $exam->uuid,
                'participant_name' => 'Test Candidate',
                'participant_index_number' => 'IND001',
                'participant_email' => 'test@example.com',
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'score' => 8.5,
                'total_marks' => 10.0,
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        $this->service->ingest($batch, $centerId);

        $this->assertDatabaseHas('general_exam_configured_participants', [
            'general_exam_id' => $exam->id,
            'unique_code' => 'IND001',
            'name' => 'Test Candidate',
        ]);
    }

    public function test_ingest_missing_uuid_returns_error(): void
    {
        $batch = [
            [
                'exam_uuid' => '01NONEXISTENTEXAM',
                'participant_name' => 'Test Candidate',
                'sync_version' => 1,
                'answers' => [],
            ],
        ];

        $result = $this->service->ingest($batch, 1);

        $this->assertEquals(0, $result['ingested']);
        $this->assertCount(1, $result['errors']);
        $this->assertStringContainsString('missing uuid', $result['errors'][0]['error']);
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

        return $exam;
    }

    protected function createExamWithQuestion(): GeneralExam
    {
        $exam = $this->createExam();

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
