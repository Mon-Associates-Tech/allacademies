<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SubmissionAnswer;

/**
 * ParityCheckService — verifies grading parity on central.
 *
 * After a submission is ingested from a center, this service re-grades
 * from raw answers using McqGrader and compares the result with the
 * center's score. A mismatch is flagged but never overwrites the center's
 * score — both scores are stored for investigation.
 */
class ParityCheckService
{
    /**
     * Check grading parity for a submission.
     *
     * Re-grades from raw answers and compares with the center's score.
     *
     * @return array{parity_ok: bool, center_score: float|null, central_score: float|null, percentage_diff: float}
     */
    public function check(GeneralExamSubmission $submission): array
    {
        $exam = $submission->exam;
        if (!$exam) {
            return [
                'parity_ok' => false,
                'center_score' => $submission->score,
                'central_score' => null,
                'percentage_diff' => 0,
            ];
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
        $revision = $exam->revision ?? 1;

        foreach ($questions as $question) {
            $qid = $question->id;
            $marksHundredths = McqGrader::toHundredths($question->marks ?? 0);
            $totalMarksHundredths += $marksHundredths;

            $answer = $answers->get($qid);
            $rawResponse = $answer?->response;

            if ($question->excluded_from_grading) {
                if ($question->award_marks_on_exclusion) {
                    $totalScoreHundredths += $marksHundredths;
                }
                continue;
            }

            if ($rawResponse === null) {
                continue;
            }

            if ($question->canAutoGrade()) {
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
            }
        }

        $centralScore = McqGrader::fromHundredths($totalScoreHundredths);
        $centerScore = $submission->score ?? 0;

        // Compare scores — allow tiny floating point tolerance
        $parityOk = abs($centralScore - $centerScore) < 0.015;

        // Calculate percentage difference for logging
        $percentageDiff = 0;
        if ($centerScore > 0) {
            $percentageDiff = abs($centralScore - $centerScore) / $centerScore * 100;
        }

        // Update submission with parity results
        $submission->update([
            'central_score' => $centralScore,
            'parity_ok' => $parityOk,
            'parity_checked_at' => now(),
        ]);

        return [
            'parity_ok' => $parityOk,
            'center_score' => $centerScore,
            'central_score' => $centralScore,
            'percentage_diff' => round($percentageDiff, 4),
        ];
    }

    /**
     * Check parity for all submissions of an exam.
     *
     * @return array{total: int, checked: int, parity_ok: int, parity_fail: int}
     */
    public function checkExam(GeneralExamSubmission $submission): array
    {
        $exam = $submission->exam;
        if (!$exam) {
            return ['total' => 0, 'checked' => 0, 'parity_ok' => 0, 'parity_fail' => 0];
        }

        $submissions = GeneralExamSubmission::where('general_exam_id', $exam->id)
            ->whereNotNull('score')
            ->whereNull('parity_checked_at')
            ->get();

        $total = $submissions->count();
        $parityOk = 0;
        $parityFail = 0;

        foreach ($submissions as $sub) {
            $result = $this->check($sub);
            if ($result['parity_ok']) {
                $parityOk++;
            } else {
                $parityFail++;
            }
        }

        return [
            'total' => $total,
            'checked' => $total,
            'parity_ok' => $parityOk,
            'parity_fail' => $parityFail,
        ];
    }
}
