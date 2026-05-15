<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\ExaminationHub\Services\ProctoringService;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProctoringController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(private readonly ProctoringService $proctoringService) {}

    // ─── Participant-facing (called by exam-proctor.js) ───────────────────────

    /**
     * POST /examinations-hub/take/{exam}/proctor/event
     * Accepts a JSON payload from the browser JS and logs the event.
     */
    public function storeEvent(Request $request, GeneralExam $exam): JsonResponse
    {
        $submissionId = session('exam_submission_id');
        $submission   = GeneralExamSubmission::find($submissionId);

        if (! $submission || $submission->general_exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid session.'], 403);
        }

        if ($submission->submitted_at) {
            return response()->json(['status' => 'already_submitted']);
        }

        $data = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'event_data' => ['nullable', 'array'],
        ]);

        $log = $this->proctoringService->logEvent(
            $submission,
            $data['event_type'],
            $data['event_data'] ?? []
        );

        $shouldAutoSubmit = $this->proctoringService->shouldAutoSubmit($exam, $submission);

        return response()->json([
            'status'            => 'logged',
            'severity'          => $log->severity,
            'should_auto_submit' => $shouldAutoSubmit,
        ]);
    }

    // ─── Admin-facing ────────────────────────────────────────────────────────

    /**
     * GET /examinations-hub/exams/{exam}/proctoring
     * Show the proctoring dashboard for an exam.
     */
    public function index(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        $summaries = $this->proctoringService->getSummaryForExam($exam);

        return view('examination-hub.proctoring.index', compact('exam', 'summaries'));
    }

    /**
     * GET /examinations-hub/exams/{exam}/submissions/{submission}/proctoring
     * Show detailed proctoring logs for one submission.
     */
    public function show(GeneralExam $exam, GeneralExamSubmission $submission): View
    {
        $this->ensureOwnerAccess($exam);
        abort_unless($submission->general_exam_id === $exam->id, 404);

        $logs    = $this->proctoringService->getLogsForSubmission($submission);
        $summary = $this->proctoringService->getSummaryForSubmission($submission);

        return view('examination-hub.proctoring.show', compact('exam', 'submission', 'logs', 'summary'));
    }
}
