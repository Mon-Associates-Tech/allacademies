<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(
        private readonly ExamSubmissionExportServiceInterface $exportService,
        private readonly GeneralExamGradingService $gradingService,
    ) {}

    public function index(Request $request, GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $sortByCombined = $request->string('sort')->toString() ?: 'submitted_at_desc';
        
        // Parse combined sort parameter (e.g., "percentage_desc" -> field: "percentage", direction: "desc")
        $parts = explode('_', $sortByCombined);
        $direction = array_pop($parts);
        $field = implode('_', $parts);

        // Whitelist to prevent SQL injection
        $allowedSorts = ['submitted_at', 'percentage', 'time_taken_minutes', 'id'];
        if (!in_array($field, $allowedSorts, true)) {
            $field = 'submitted_at';
        }
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query = $exam->submissions();

        // 1. Calculate Summary Metrics on the FILTERED dataset
        $summaryQuery = clone $query;
        if ($search !== '') {
            $summaryQuery->where(function ($q) use ($search) {
                $q->where('participant_name', 'like', "%{$search}%")
                  ->orWhere('participant_email', 'like', "%{$search}%");
            });
        }
        if ($status !== '') {
            $summaryQuery->where('status', $status);
        }

        $summary = [
            'total'     => $summaryQuery->count(),
            'avg_score' => (float) ($summaryQuery->avg('percentage') ?? 0),
            'max_score' => (float) ($summaryQuery->max('percentage') ?? 0),
            'min_score' => (float) ($summaryQuery->min('percentage') ?? 0),
        ];

        // 2. Apply Filters to Main Paginated Query
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('participant_name', 'like', "%{$search}%")
                  ->orWhere('participant_email', 'like', "%{$search}%");
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }

        $submissions = $query->orderBy($field, $direction)->paginate(20)->withQueryString();

        return view('examination-hub.submissions.index', [
            'exam'        => $exam,
            'submissions' => $submissions,
            'summary'     => $summary,
            'filters'     => [
                'search' => $search,
                'status' => $status,
                'sort'   => $sortByCombined,
            ],
        ]);
    }

    public function show(GeneralExam $exam, GeneralExamSubmission $submission): View
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        return view('examination-hub.submissions.show', [
            'exam' => $exam,
            'submission' => $submission,
        ]);
    }

    public function grade(GeneralExam $exam, GeneralExamSubmission $submission): View
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        return view('examination-hub.submissions.grade', [
            'exam' => $exam,
            'submission' => $submission,
        ]);
    }

    /**
     * Manually override a single question's score for a submission.
     * Used by administrators in rare situations where auto-grading is wrong.
     */
    public function manualGrade(Request $request, GeneralExam $exam, GeneralExamSubmission $submission): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:general_exam_questions,id'],
            'points'      => ['required', 'numeric', 'min:0'],
            'feedback'    => ['nullable', 'string', 'max:1000'],
        ]);

        $question = GeneralExamQuestion::find($validated['question_id']);
        abort_unless($question->general_exam_id === $exam->id, 422);

        $this->gradingService->manualGradeQuestion(
            $submission,
            $question->id,
            (float) $validated['points'],
            $validated['feedback'] ?? null,
            auth()->id()
        );

        return back()->with('success', 'Score updated successfully.');
    }

    public function applyBonus(Request $request, GeneralExam $exam, GeneralExamSubmission $submission): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        $validated = $request->validate([
            'bonus_points' => ['required', 'numeric', 'min:0', 'max:100'],
            'bonus_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->gradingService->applyBonus(
            $submission,
            (float) $validated['bonus_points'],
            $validated['bonus_reason'] ?? null,
            auth()->id()
        );

        return back()->with('success', 'Bonus applied successfully.');
    }

    public function removeBonus(GeneralExam $exam, GeneralExamSubmission $submission): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        $this->gradingService->applyBonus($submission, 0, null, auth()->id());

        return back()->with('success', 'Bonus removed.');
    }

    public function removeBonusAll(GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $count = $exam->submissions()
            ->where('bonus_points', '>', 0)
            ->get()
            ->each(fn($s) => $this->gradingService->applyBonus($s, 0, null, auth()->id()))
            ->count();

        return back()->with('success', "Bonus removed from {$count} submission(s).");
    }

    public function applyBonusAll(Request $request, GeneralExam $exam): RedirectResponse
    {
        $this->ensureOwnerAccess($exam);

        $validated = $request->validate([
            'bonus_points' => ['required', 'numeric', 'min:0', 'max:100'],
            'bonus_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $submissions = $exam->submissions()
            ->whereNotNull('submitted_at')
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_SUBMITTED,
                GeneralExamSubmission::STATUS_AUTO_GRADED,
                GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
                GeneralExamSubmission::STATUS_FINAL,
            ])
            ->get();

        foreach ($submissions as $submission) {
            $this->gradingService->applyBonus(
                $submission,
                (float) $validated['bonus_points'],
                $validated['bonus_reason'] ?? null,
                auth()->id()
            );
        }

        return back()->with('success', "Bonus applied to {$submissions->count()} submission(s).");
    }

    public function export(GeneralExam $exam): StreamedResponse
    {
        $this->ensureOwnerAccess($exam);

        return $this->exportService->exportCsv($exam);
    }

    /** Export as Excel (.xlsx). Add the route: GET /exams/{exam}/submissions/export-excel */
    public function exportExcel(GeneralExam $exam): StreamedResponse
    {
        $this->ensureOwnerAccess($exam);

        return $this->exportService->exportExcel($exam);
    }
}