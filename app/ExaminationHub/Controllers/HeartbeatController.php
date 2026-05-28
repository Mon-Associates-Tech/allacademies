<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamAdminMessage;
use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Services\LiveMonitoringService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    public function __construct(
        private readonly LiveMonitoringService $monitoringService
    ) {}

    /**
     * POST /examinations-hub/take/{exam}/heartbeat
     * Called periodically by participant's browser (every 10-30 seconds).
     */
    public function beat(Request $request, GeneralExam $exam): JsonResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (! $submission || $submission->general_exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        // Get or create heartbeat session
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)->first();

        if (! $heartbeat) {
            // Initialize session if not exists
            $heartbeat = $this->monitoringService->initializeSession($submission, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $request->input('browser'),
                'os' => $request->input('os'),
            ]);
        }

        // Check if terminated by admin
        if ($heartbeat->status === ExamParticipantHeartbeat::STATUS_TERMINATED) {
            // Get the termination message for display before redirecting
            $terminationMessage = ExamAdminMessage::forSubmission($submission->id)
                ->ofType(ExamAdminMessage::TYPE_TERMINATION)
                ->orderByDesc('created_at')
                ->first();

            return response()->json([
                'status' => 'terminated',
                'reason' => $heartbeat->termination_reason,
                'message' => $terminationMessage?->message ?? 'Your exam session has been terminated by the administrator.',
                'redirect' => route('examination-hub.take.completed', $exam),
            ]);
        }

        // Process heartbeat data
        $data = $request->validate([
            'is_focused' => ['nullable', 'boolean'],
            'current_question_index' => ['nullable', 'integer', 'min:0'],
            'current_section_index' => ['nullable', 'integer', 'min:0'],
            'questions_answered' => ['nullable', 'integer', 'min:0'],
        ]);

        $heartbeat = $this->monitoringService->processHeartbeat($heartbeat->session_token, $data);

        // Compute questions_answered from submission ground truth
        $submission->refresh();
        $answeredCount = count(array_filter(
            $submission->responses ?? [],
            fn ($r) => ! empty($r['response'] ?? $r)
        ));
        if ($heartbeat->questions_answered !== $answeredCount) {
            $heartbeat->update(['questions_answered' => $answeredCount]);
            $heartbeat->questions_answered = $answeredCount;
        }

        // Build response
        $response = [
            'status' => 'ok',
            'session_token' => $heartbeat->session_token,
            'server_time' => now()->toIso8601String(),
        ];

        // Include warning if present
        if ($heartbeat->has_warning) {
            $response['warning'] = [
                'message' => $heartbeat->admin_message,
                'warned_at' => $heartbeat->warned_at->toIso8601String(),
            ];
        }

        // Check for undelivered admin messages (from audit log)
        $undeliveredMessages = ExamAdminMessage::forSubmission($submission->id)
            ->undelivered()
            ->orderBy('created_at')
            ->get();

        if ($undeliveredMessages->isNotEmpty()) {
            // Get the most recent message to display
            $latestMessage = $undeliveredMessages->last();
            
            $response['admin_message'] = [
                'message' => $latestMessage->message,
                'type' => $latestMessage->message_type,
                'sent_at' => $latestMessage->created_at->toIso8601String(),
            ];

            // Mark all undelivered messages as delivered
            foreach ($undeliveredMessages as $msg) {
                $msg->markDelivered();
            }
        } elseif (! $heartbeat->has_warning && $heartbeat->admin_message) {
            // Fallback: legacy admin_message from heartbeat table
            $response['admin_message'] = [
                'message' => $heartbeat->admin_message,
                'type' => 'info',
            ];
            // Clear it after delivery
            $heartbeat->update(['admin_message' => null]);
        }

        return response()->json($response);
    }

    /**
     * POST /examinations-hub/take/{exam}/heartbeat/init
     * Initialize heartbeat session when exam starts.
     */
    public function initialize(Request $request, GeneralExam $exam): JsonResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (! $submission || $submission->general_exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        $deviceInfo = $request->validate([
            'browser' => ['nullable', 'string', 'max:100'],
            'os' => ['nullable', 'string', 'max:100'],
            'screen_width' => ['nullable', 'integer'],
            'screen_height' => ['nullable', 'integer'],
        ]);

        $deviceInfo['ip'] = $request->ip();
        $deviceInfo['user_agent'] = $request->userAgent();

        $heartbeat = $this->monitoringService->initializeSession($submission, $deviceInfo);

        return response()->json([
            'status' => 'initialized',
            'session_token' => $heartbeat->session_token,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /examinations-hub/take/{exam}/heartbeat/acknowledge-warning
     * Participant acknowledges warning.
     */
    public function acknowledgeWarning(Request $request, GeneralExam $exam): JsonResponse
    {
        $submissionId = session('exam_submission_id');
        $submission = GeneralExamSubmission::find($submissionId);

        if (! $submission || $submission->general_exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        $this->monitoringService->clearWarning($submission->id);

        return response()->json(['status' => 'acknowledged']);
    }
}
