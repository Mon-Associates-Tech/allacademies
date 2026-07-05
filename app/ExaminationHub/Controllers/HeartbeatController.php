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

        // ── Single-session enforcement ────────────────────────────────────────
        // Each authenticate() writes a fresh device_token to the submission and
        // stores it in the PHP session.  If the token in the session doesn't
        // match the one on the submission, a second device has since authenticated
        // and this device must be kicked out.
        //
        // We do NOT check this for submissions that have no device_token yet
        // (existing rows created before this feature was deployed).
        if (
            $submission->device_token &&
            session('exam_device_token') !== $submission->device_token
        ) {
            return response()->json([
                'status'   => 'session_superseded',
                'message'  => 'This exam session has been opened on another device. '
                            . 'You have been logged out of this session.',
                'redirect' => route('examination-hub.take.join'),
            ]);
        }

        // Submission already completed by admin force-submit or other server action
        if ($submission->submitted_at) {
            return response()->json([
                'status'   => 'force_submitted',
                'message'  => $submission->auto_submit_reason ?? 'Your exam has been submitted by the administrator.',
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
            'status'       => 'ok',
            'session_token' => $heartbeat->session_token,
            'server_time'  => now()->toIso8601String(),
        ];

        // ── Authoritative time information ────────────────────────────────────
        // The client timer re-syncs from these values on every heartbeat so that
        // admin-granted extensions are reflected without needing a page reload.
        // getRemainingTime() already incorporates extra_time_minutes.
        // Wrapped in try-catch so any failure here never converts a valid heartbeat
        // into a 500 response (which would stop the candidate appearing as active).
        try {
            $submission->refresh(); // ensure extra_time_minutes is current
            $remainingSeconds = $submission->getRemainingTime();

            if ($remainingSeconds !== null) {
                $response['remaining_seconds']     = $remainingSeconds;
                $response['total_allowed_seconds'] = $submission->getTotalAllowedSeconds();
                $response['extra_time_minutes']    = $submission->extra_time_minutes ?? 0;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('HeartbeatController: failed to compute remaining time', [
                'submission_id' => $submission->id,
                'error'         => $e->getMessage(),
            ]);
        }

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

        $response = [
            'status' => 'initialized',
            'session_token' => $heartbeat->session_token,
            'server_time' => now()->toIso8601String(),
        ];

        // Include authoritative remaining time when available so the client
        // can immediately initialise the timer without waiting for the
        // first periodic heartbeat poll.
        try {
            $submission->refresh();
            $remainingSeconds = $submission->getRemainingTime();
            if ($remainingSeconds !== null) {
                $response['remaining_seconds']     = $remainingSeconds;
                $response['total_allowed_seconds'] = $submission->getTotalAllowedSeconds();
                $response['extra_time_minutes']    = $submission->extra_time_minutes ?? 0;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('HeartbeatController.initialize: failed to compute remaining time', [
                'submission_id' => $submission->id,
                'error'         => $e->getMessage(),
            ]);
        }

        return response()->json($response);
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
