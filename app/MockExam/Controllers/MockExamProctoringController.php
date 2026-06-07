<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Services\MockExamProctoringService;
use Illuminate\Http\Request;

class MockExamProctoringController extends Controller
{
    public function __construct(
        private readonly MockExamProctoringService $proctoringService
    ) {
    }

    public function recordEvent(Request $request, MockExam $mockExam)
    {
        $validated = $request->validate([
            'event_type' => 'required|in:tab_switch,fullscreen_exit,paste_attempt,focus_loss',
            'details' => 'nullable|array',
        ]);

        $submissionId = session("mock_exam_{$mockExam->id}_submission_id");
        if (! $submissionId) {
            return response()->json(['error' => 'No active submission'], 403);
        }

        $submission = MockExamSubmission::find($submissionId);
        if (! $submission || $submission->mock_exam_id !== $mockExam->id) {
            return response()->json(['error' => 'Invalid submission'], 403);
        }

        $this->proctoringService->recordEvent(
            $submission,
            $validated['event_type'],
            $validated['details'] ?? null
        );

        $shouldSubmit = $this->proctoringService->shouldAutoSubmit($submission);
        $violationCount = $this->proctoringService->getViolationCount($submission);
        $limit = $mockExam->getViolationLimit();

        $warningMessage = null;
        if ($limit > 0 && $violationCount >= $limit - 1 && ! $shouldSubmit) {
            $warningMessage = 'Warning: One more violation will auto-submit your exam.';
        }

        return response()->json([
            'should_submit' => $shouldSubmit,
            'violation_count' => $violationCount,
            'warning_message' => $warningMessage,
        ]);
    }
}
