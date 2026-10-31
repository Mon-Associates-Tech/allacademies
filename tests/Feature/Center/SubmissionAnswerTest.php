<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\ExaminationHub\Services\GradeScale;
use App\ExaminationHub\Services\McqGrader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionAnswerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run all Phase 0 and Phase 1 migrations
        $this->artisan('migrate:fresh', [
            '--path' => database_path('migrations'),
        ]);
    }

    // ─── SubmissionAnswer model ───────────────────────────────────────────────

    public function test_upsert_answer_creates_new_row(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'What is 2+2?', 'down' => 'What is 2+2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        $answer = SubmissionAnswer::updateOrCreate(
            [
                'general_exam_submission_id' => $submission->id,
                'general_exam_question_id' => $question->id,
            ],
            [
                'uuid' => (string) \Illuminate\Support\Str::ulid(),
                'question_uuid' => $question->uuid,
                'response' => 'B',
                'answered_at' => now(),
            ]
        );

        $this->assertDatabaseHas('submission_answers', [
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'response' => 'B',
        ]);

        $this->assertNotNull($answer->uuid);
        $this->assertEquals($question->uuid, $answer->question_uuid);
    }

    public function test_upsert_answer_updates_existing_row(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'What is 2+2?', 'down' => 'What is 2+2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        // Create initial answer
        SubmissionAnswer::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'question_uuid' => $question->uuid,
            'response' => 'A',
            'answered_at' => now(),
        ]);

        // Update the answer
        SubmissionAnswer::updateOrCreate(
            [
                'general_exam_submission_id' => $submission->id,
                'general_exam_question_id' => $question->id,
            ],
            [
                'response' => 'B',
                'answered_at' => now(),
            ]
        );

        $this->assertDatabaseHas('submission_answers', [
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'response' => 'B',
        ]);

        // Only one row should exist
        $count = SubmissionAnswer::where(
            'general_exam_submission_id',
            $submission->id
        )->where(
            'general_exam_question_id',
            $question->id
        )->count();

        $this->assertEquals(1, $count);
    }

    // ─── GeneralExamSubmission methods ────────────────────────────────────────

    public function test_save_response_upserts_answer(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'What is 2+2?', 'down' => 'What is 2+2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        $submission->saveResponse($question->id, 'b');

        $this->assertDatabaseHas('submission_answers', [
            'general_exam_submission_id' => $submission->id,
            'general_exam_question_id' => $question->id,
            'response' => 'B', // Normalized to uppercase
        ]);
    }

    public function test_get_response_reads_from_answers_table(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'What is 2+2?', 'down' => 'What is 2+2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        $submission->saveResponse($question->id, 'C');

        $this->assertEquals('C', $submission->getResponse($question->id));
    }

    public function test_has_answered_checks_answers_table(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'What is 2+2?', 'down' => 'What is 2+2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        $this->assertFalse($submission->hasAnswered($question->id));

        $submission->saveResponse($question->id, 'A');

        $this->assertTrue($submission->hasAnswered($question->id));
    }

    public function test_get_answered_count(): void
    {
        $exam = GeneralExam::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'title' => 'Math Exam',
            'user_id' => 1,
            'school_id' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'duration_in_minutes' => 60,
        ]);

        $section = GeneralExamSection::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'title' => 'Section 1',
            'order' => 0,
        ]);

        $question1 = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'Q1?', 'down' => 'Q1?'],
            'correct_answer' => 'A',
            'marks' => 1.0,
            'order' => 0,
        ]);

        $question2 = GeneralExamQuestion::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'general_exam_section_id' => $section->id,
            'type' => 'multiple_choice',
            'question' => ['up' => 'Q2?', 'down' => 'Q2?'],
            'correct_answer' => 'B',
            'marks' => 1.0,
            'order' => 1,
        ]);

        $submission = GeneralExamSubmission::create([
            'uuid' => (string) \Illuminate\Support\Str::ulid(),
            'general_exam_id' => $exam->id,
            'participant_type' => 'general',
            'participant_id' => 1,
            'participant_name' => 'Test User',
            'status' => GeneralExamSubmission::STATUS_IN_PROGRESS,
        ]);

        $this->assertEquals(0, $submission->getAnsweredCount());

        $submission->saveResponse($question1->id, 'A');
        $this->assertEquals(1, $submission->getAnsweredCount());

        $submission->saveResponse($question2->id, 'B');
        $this->assertEquals(2, $submission->getAnsweredCount());
    }

    // ─── GradeScale ───────────────────────────────────────────────────────────

    public function test_grade_scale_default(): void
    {
        $this->assertEquals('A+', GradeScale::resolve(95, null));
        $this->assertEquals('A', GradeScale::resolve(85, null));
        $this->assertEquals('B', GradeScale::resolve(75, null));
        $this->assertEquals('C', GradeScale::resolve(65, null));
        $this->assertEquals('D', GradeScale::resolve(55, null));
        $this->assertEquals('F', GradeScale::resolve(40, null));
    }

    public function test_grade_scale_custom(): void
    {
        $scale = [
            ['min' => 80, 'max' => 100, 'grade' => 'Distinction'],
            ['min' => 50, 'max' => 79, 'grade' => 'Pass'],
            ['min' => 0, 'max' => 49, 'grade' => 'Fail'],
        ];

        $this->assertEquals('Distinction', GradeScale::resolve(90, $scale));
        $this->assertEquals('Pass', GradeScale::resolve(60, $scale));
        $this->assertEquals('Fail', GradeScale::resolve(30, $scale));
    }

    public function test_grade_scale_default_array(): void
    {
        $default = GradeScale::defaultArray();
        $this->assertCount(6, $default);
        $this->assertEquals('A+', $default[0]['grade']);
        $this->assertEquals('F', $default[5]['grade']);
    }
}
