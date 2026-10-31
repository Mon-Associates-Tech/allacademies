<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;
use App\Jobs\ExaminationHub\GradeSubmissionJob;
use Illuminate\Support\Facades\DB;

class ExamGradingService
{
    /**
     * Dispatch grading as a background job (preferred for submission flow).
     */
    public function dispatchGrading(GeneralExamSubmission $submission): void
    {
        GradeSubmissionJob::dispatch($submission->id);
    }

    /**
     * Grade synchronously — use only when a queued result is needed immediately
     * (e.g. force-submit from admin, tests).
     *
     * Now uses McqGrader and reads from submission_answers table.
     */
    public function grade(GeneralExamSubmission $submission): void
    {
        $exam = $submission->assignment;
        if (! $exam) {
            return;
        }

        // Load all questions for this exam
        $questions = $exam->questions;
        if ($questions->isEmpty()) {
            $questions = $exam->load('questions')->questions;
        }

        // Load all answers from submission_answers for this submission
        $answers = SubmissionAnswer::where(
            'general_exam_submission_id',
            $submission->id
        )->get()->keyBy('general_exam_question_id');

        $totalScoreHundredths = 0;
        $totalMarksHundredths = 0;
        $needsReview = false;
        $revision = $exam->revision ?? 1;

        DB::transaction(function () use ($submission, $questions, $answers, &$totalScoreHundredths, &$totalMarksHundredths, &$needsReview, $revision, $exam) {
            foreach ($questions as $question) {
                $qid = $question->id;
                $marksHundredths = McqGrader::toHundredths($question->marks ?? 0);
                $totalMarksHundredths += $marksHundredths;

                $answer = $answers->get($qid);
                $rawResponse = $answer?->response;

                if ($question->excluded_from_grading) {
                    // Handle excluded questions
                    if ($question->award_marks_on_exclusion) {
                        $totalScoreHundredths += $marksHundredths;
                    }

                    // Update the answer row with grading info
                    if ($answer) {
                        $answer->update([
                            'is_correct' => null,
                            'points_earned' => $question->award_marks_on_exclusion ? McqGrader::fromHundredths($marksHundredths) : 0,
                            'graded_revision' => $revision,
                        ]);
                    }
                    continue;
                }

                if ($rawResponse === null) {
                    // No answer provided
                    if ($answer) {
                        $answer->update([
                            'is_correct' => false,
                            'points_earned' => 0,
                            'graded_revision' => $revision,
                        ]);
                    }
                    continue;
                }

                if ($question->canAutoGrade()) {
                    // Use McqGrader for MCQ and True/False
                    $questionData = [
                        'correct_answer' => $question->correct_answer ?? '',
                        'marks' => $question->marks ?? 0,
                        'excluded_from_grading' => (bool) $question->excluded_from_grading,
                        'award_marks_on_exclusion' => (bool) $question->award_marks_on_exclusion,
                    ];

                    if ($question->isMultipleChoice()) {
                        $result = McqGrader::grade($questionData, $rawResponse);
                    } else {
                        $result = McqGrader::gradeTrueFalse($questionData, $rawResponse);
                    }

                    $totalScoreHundredths += $result['points_earned'];

                    if ($answer) {
                        $answer->update([
                            'is_correct' => $result['is_correct'],
                            'points_earned' => $result['points_earned_decimal'],
                            'graded_revision' => $revision,
                        ]);
                    }
                } else {
                    // Requires manual grading (essay, short answer)
                    $needsReview = true;

                    if ($answer) {
                        $answer->update([
                            'is_correct' => null,
                            'points_earned' => 0,
                            'graded_revision' => $revision,
                        ]);
                    }
                }
            }

            // Calculate percentage using integer hundredths
            $percentageHundredths = McqGrader::percentage($totalScoreHundredths, $totalMarksHundredths);
            $percentage = McqGrader::percentageToFloat($percentageHundredths);

            // Resolve grade from the exam's embedded grading scale
            $grade = GradeScale::resolve($percentage, $exam->grading_scale);

            $scoreDecimal = McqGrader::fromHundredths($totalScoreHundredths);
            $totalMarksDecimal = McqGrader::fromHundredths($totalMarksHundredths);

            $submission->update([
                'score' => $scoreDecimal,
                'total_marks' => $totalMarksDecimal,
                'percentage' => $percentage,
                'grade' => $grade,
                'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
                'requires_manual_review' => $needsReview,
                'graded_at' => now(),
                'graded_revision' => $revision,
            ]);

            // Increment sync_version so push agent knows this submission changed
            $submission->increment('sync_version');
        });
    }
}
