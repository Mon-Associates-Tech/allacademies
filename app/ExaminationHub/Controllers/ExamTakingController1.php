<?php

/**
 * Drop-in replacement for ExamTakingController.
 *
 * Changes from the original:
 *  1. Injects GradingSystemService so calculateGrade() uses the admin-configured
 *     grade scales instead of the hardcoded match expression.
 *  2. Passes user_id + school_id to the service for multi-tenant resolution.
 */

namespace App\Http\Controllers\Examinations;

use App\ExaminationHub\Services\GradingSystemService;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamTakingController extends Controller
{
    public function __construct(private readonly GradingSystemService $gradingService) {}

    // ── All unchanged methods are omitted here for brevity.
    // ── Copy them verbatim from the original ExamTakingController.php.
    // ── Only autoGradeSubmission() and calculateGrade() change. ────────────

    private function autoGradeSubmission(GeneralExamSubmission $submission, GeneralExam $exam): void
    {
        $exam->load('questions');
        $responses      = $submission->responses ?? [];
        $totalScore     = 0;
        $totalMarks     = 0;
        $gradedResponses = [];

        foreach ($exam->questions as $question) {
            $questionId  = $question->id;
            $totalMarks += $question->marks;
            $response    = $responses[$questionId]['response'] ?? null;

            if ($response === null) {
                $gradedResponses[$questionId] = [
                    'response'    => null,
                    'is_correct'  => false,
                    'points_earned' => 0,
                    'answered_at' => null,
                ];
                continue;
            }

            if ($question->canAutoGrade()) {
                $gradeResult = $question->gradeResponse($response);
                $gradedResponses[$questionId] = array_merge($responses[$questionId], $gradeResult);
                $totalScore += $gradeResult['points_earned'];
            } else {
                $gradedResponses[$questionId] = array_merge($responses[$questionId], [
                    'is_correct'      => null,
                    'points_earned'   => 0,
                    'requires_grading' => true,
                ]);
            }
        }

        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        // ── Use the configurable grading system ──────────────────────────────
        $grade = $this->calculateGrade($percentage, $exam);

        $submission->update([
            'responses'          => $gradedResponses,
            'score'              => $totalScore,
            'total_marks'        => $totalMarks,
            'percentage'         => round($percentage, 2),
            'grade'              => $grade,
            'status'             => 'auto_graded',
        ]);
    }

    /**
     * Resolve a percentage to a grade label.
     *
     * Priority: exam owner's custom grade scales → built-in fallback.
     */
    private function calculateGrade(float $percentage, GeneralExam $exam): string
    {
        return $this->gradingService->resolveGrade(
            $percentage,
            $exam->user_id,
            $exam->school_id
        );
    }
}
