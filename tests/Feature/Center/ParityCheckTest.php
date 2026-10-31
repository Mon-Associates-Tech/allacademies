<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Services\ParityCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParityCheckTest extends TestCase
{
    use RefreshDatabase;

    protected ParityCheckService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ParityCheckService::class);
    }

    public function test_parity_ok_when_scores_match(): void
    {
        [$exam, $submission, $answer] = $this->createGradedSubmission(1.0, 1.0, 100.0, 'A');

        $result = $this->service->check($submission);

        $this->assertTrue($result['parity_ok']);
        $this->assertEquals(1.0, $result['center_score']);
        $this->assertEquals(1.0, $result['central_score']);
        $this->assertNotNull($submission->fresh()->parity_checked_at);
    }

    public function test_parity_fail_when_scores_differ(): void
    {
        [$exam, $submission, $answer] = $this->createGradedSubmission(0.5, 1.0, 50.0, 'C');
        // Manipulate the center score to simulate a mismatch
        $submission->update(['score' => 0.0]);

        $result = $this->service->check($submission);

        $this->assertFalse($result['parity_ok']);
        $this->assertEquals(0.0, $result['center_score']);
        $this->assertEquals(1.0, $result['central_score']);
    }

    public function test_parity_with_excluded_question(): void
    {
        $exam = $this->createExamWithExcludedQuestion();
        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_name' => 'Test Candidate',
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'score' => 0.0,
            'total_marks' => 1.0,
            'percentage' => 0.0,
            'grade' => 'F',
            'sync_version' => 1,
            'synced_version' => 1,
        ]);

        // Answer the non-excluded question correctly
        $question = $exam->questions->filter(fn($q) => !$q->excluded_from_grading)->first();
        SubmissionAnswer::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'question_uuid' => $question->uuid,
            'response' => 'A',
            'answered_at' => now(),
        ]);

        $result = $this->service->check($submission);

        // Central re-grades: excluded question gives 0, answered question gives 1.0
        $this->assertEquals(1.0, $result['central_score']);
    }

    public function test_parity_with_award_marks_on_exclusion(): void
    {
        $exam = $this->createExamWithAwardedExclusion();
        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_name' => 'Test Candidate',
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'score' => 1.0, // Center awarded marks on exclusion
            'total_marks' => 1.0,
            'percentage' => 100.0,
            'grade' => 'A',
            'sync_version' => 1,
            'synced_version' => 1,
        ]);

        $result = $this->service->check($submission);

        $this->assertTrue($result['parity_ok']);
        $this->assertEquals(1.0, $result['central_score']);
    }

    public function test_parity_with_no_answer(): void
    {
        [$exam, $submission, $answer] = $this->createGradedSubmission(0.0, 1.0, 0.0, 'F');
        // Delete the answer to simulate no answer
        $answer->delete();

        $result = $this->service->check($submission);

        $this->assertTrue($result['parity_ok']);
        $this->assertEquals(0.0, $result['central_score']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    protected function createGradedSubmission(float $score, float $totalMarks, float $percentage, string $grade): array
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

        $question = GeneralExamQuestion::create([
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

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_name' => 'Test Candidate',
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'score' => $score,
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'grade' => $grade,
            'sync_version' => 1,
            'synced_version' => 1,
        ]);

        $answer = SubmissionAnswer::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'question_uuid' => $question->uuid,
            'response' => $score > 0 ? 'A' : 'B',
            'answered_at' => now(),
        ]);

        return [$exam, $submission, $answer];
    }

    protected function createExamWithExcludedQuestion(): GeneralExam
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

        // Excluded question (no award)
        GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_section_id' => $section->id,
            'type' => GeneralExamQuestion::TYPE_MULTIPLE_CHOICE,
            'question' => ['up' => 'Excluded question?', 'down' => null],
            'options' => [
                ['key' => 'A', 'value' => ['up' => 'Yes', 'down' => null]],
                ['key' => 'B', 'value' => ['up' => 'No', 'down' => null]],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'excluded_from_grading' => true,
            'award_marks_on_exclusion' => false,
            'order' => 1,
        ]);

        // Normal question
        GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_section_id' => $section->id,
            'type' => GeneralExamQuestion::TYPE_MULTIPLE_CHOICE,
            'question' => ['up' => 'Normal question?', 'down' => null],
            'options' => [
                ['key' => 'A', 'value' => ['up' => 'Yes', 'down' => null]],
                ['key' => 'B', 'value' => ['up' => 'No', 'down' => null]],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'order' => 2,
        ]);

        return $exam;
    }

    protected function createExamWithAwardedExclusion(): GeneralExam
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
            'question' => ['up' => 'Excluded with award?', 'down' => null],
            'options' => [
                ['key' => 'A', 'value' => ['up' => 'Yes', 'down' => null]],
                ['key' => 'B', 'value' => ['up' => 'No', 'down' => null]],
            ],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'excluded_from_grading' => true,
            'award_marks_on_exclusion' => true,
            'order' => 1,
        ]);

        return $exam;
    }
}
