<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Jobs\ExaminationHub\GradeSubmissionJob;

class ExamGradingService
{
    public function __construct(
        private readonly GradingSystemService $gradingSystemService
    ) {}

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
     */
    public function grade(GeneralExamSubmission $submission): void
    {
        $exam = $submission->assignment()->with('questions')->first();

        if (! $exam) {
            return;
        }

        $responses = $submission->responses ?? [];
        $totalScore = 0;
        $totalMarks = 0;
        $graded = [];
        $needsReview = false;

        foreach ($exam->questions as $question) {
            $qid = $question->id;
            $totalMarks += $question->marks;
            $raw = $responses[$qid]['response'] ?? null;

            if ($raw === null) {
                $graded[$qid] = [
                    'response' => null,
                    'is_correct' => false,
                    'points_earned' => 0,
                    'answered_at' => null,
                ];

                continue;
            }

            if ($question->canAutoGrade()) {
                $result = $question->gradeResponse((string) $raw);
                $graded[$qid] = array_merge($responses[$qid], $result);
                $totalScore += $result['points_earned'];
            } else {
                $needsReview = true;
                $graded[$qid] = array_merge($responses[$qid], [
                    'is_correct' => null,
                    'points_earned' => 0,
                    'requires_grading' => true,
                ]);
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        $submission->update([
            'responses' => $graded,
            'score' => $totalScore,
            'total_marks' => $totalMarks,
            'percentage' => round($percentage, 2),
            'grade' => $this->resolveGrade($percentage, $exam),
            'status' => GeneralExamSubmission::STATUS_AUTO_GRADED,
            'requires_manual_review' => $needsReview,
            'graded_at' => now(),
        ]);
    }

    private function resolveGrade(float $percentage, GeneralExam $exam): string
    {
        return $this->gradingSystemService->resolveGrade(
            $percentage,
            $exam->user_id,
            $exam->school_id
        );
    }
}
