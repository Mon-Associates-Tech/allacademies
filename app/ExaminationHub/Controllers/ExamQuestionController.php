<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(private readonly GeneralExamGradingService $gradingService) {}

    public function toggleGrading(Request $request, GeneralExam $exam, GeneralExamQuestion $question): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($question->general_exam_id === $exam->id, 404);

        $nowExcluded = ! $question->excluded_from_grading;

        $question->update([
            'excluded_from_grading'    => $nowExcluded,
            'award_marks_on_exclusion' => $nowExcluded ? (bool) $request->boolean('award_marks') : false,
        ]);

        $submissions = $exam->submissions()
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            ])
            ->whereNotNull('submitted_at')
            ->get();

        foreach ($submissions as $submission) {
            $this->gradingService->gradeSubmission($submission);
        }

        $action = $nowExcluded ? 'removed from' : 'restored to';

        return back()->with(
            'success',
            "Question #{$question->order} {$action} grading. {$submissions->count()} submission(s) regraded."
        );
    }
}
