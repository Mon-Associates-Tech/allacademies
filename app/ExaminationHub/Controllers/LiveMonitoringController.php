<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamAdminMessage;
use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Services\LiveMonitoringService;
use App\ExaminationHub\Traits\EnsuresExamOwnership;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveMonitoringController extends Controller
{
    use EnsuresExamOwnership;

    public function __construct(
        private readonly LiveMonitoringService $monitoringService
    ) {}

    // ─── Views ───────────────────────────────────────────────────────────────

    /**
     * GET /examinations/exams/{exam}/live-monitoring
     * Display the live monitoring dashboard.
     */
    public function index(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getExamMonitoringData($exam);

        return view('examination-hub.live-monitoring.index', [
            'exam'         => $exam,
            'initialData'  => $data,
        ]);
    }

    /**
     * GET /examinations/exams/{exam}/live-monitoring/{submission}
     * View detailed live data for a single participant.
     */
    public function show(GeneralExam $exam, int $submissionId): View
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getParticipantLiveData($submissionId);

        abort_if(!$data, 404, 'Participant session not found.');

        return view('examination-hub.live-monitoring.show', [
            'exam'        => $exam,
            'participant' => $data['participant'],
            'violations'  => $data['violations'],
        ]);
    }

    // ─── API Endpoints ───────────────────────────────────────────────────────

    /**
     * GET /examinations/exams/{exam}/live-monitoring/api/participants
     * Get current participant data (for polling fallback).
     */
    public function apiParticipants(GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getExamMonitoringData($exam);

        return response()->json($data);
    }

    /**
     * GET /examinations/exams/{exam}/live-monitoring/api/participant/{submission}
     * Get single participant's live data.
     */
    public function apiParticipant(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getParticipantLiveData($submissionId);

        if (!$data) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json($data);
    }

    // ─── Admin Actions ───────────────────────────────────────────────────────

    /**
     * POST /examinations/exams/{exam}/live-monitoring/warn/{submission}
     * Send warning to participant.
     */
    public function warn(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $heartbeat = $this->monitoringService->sendWarning($submissionId, $data['message']);

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json([
            'status'  => 'warning_sent',
            'message' => $data['message'],
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/message/{submission}
     * Send message to participant.
     */
    public function message(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $heartbeat = $this->monitoringService->sendMessage($submissionId, $data['message']);

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json([
            'status'  => 'message_sent',
            'message' => $data['message'],
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/message-all
     * Send message to all active participants.
     */
    public function messageAll(Request $request, GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $result = $this->monitoringService->sendMessageToAllActiveParticipants($exam, $data['message']);

        return response()->json([
            'status' => 'messages_sent',
            'sent_count' => $result['sent_count'],
            'failed_count' => $result['failed_count'],
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/terminate/{submission}
     * Terminate participant session.
     */
    public function terminate(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $heartbeat = $this->monitoringService->terminateParticipant(
            $submissionId,
            auth()->id(),
            $data['reason']
        );

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json([
            'status' => 'terminated',
            'reason' => $data['reason'],
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/force-submit/{submission}
     * Force submit participant's exam.
     */
    public function forceSubmit(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $heartbeat = $this->monitoringService->forceSubmit(
            $submissionId,
            auth()->id(),
            $data['reason'] ?? 'Admin forced submission'
        );

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json([
            'status' => 'force_submitted',
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/extend-time/{submission}
     * Extend time for participant.
     */
    public function extendTime(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'minutes' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $heartbeat = $this->monitoringService->extendTime($submissionId, $data['minutes']);

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json([
            'status'  => 'time_extended',
            'minutes' => $data['minutes'],
        ]);
    }

    /**
     * POST /examinations/exams/{exam}/live-monitoring/clear-warning/{submission}
     * Clear warning for participant.
     */
    public function clearWarning(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $heartbeat = $this->monitoringService->clearWarning($submissionId);

        if (!$heartbeat) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return response()->json(['status' => 'warning_cleared']);
    }

    /**
     * GET /examinations/exams/{exam}/live-monitoring/messages/{submission}
     * Get message history for a participant (audit trail).
     */
    public function getMessageHistory(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $messages = ExamAdminMessage::forSubmission($submissionId)
            ->with('sender')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message_type' => $message->message_type,
                    'type_label' => $message->getTypeLabel(),
                    'message' => $message->message,
                    'metadata' => $message->metadata,
                    'sent_by' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                    ] : null,
                    'sent_at' => $message->created_at->toIso8601String(),
                    'delivered_at' => $message->delivered_at?->toIso8601String(),
                    'acknowledged_at' => $message->acknowledged_at?->toIso8601String(),
                    'is_delivered' => $message->isDelivered(),
                    'is_acknowledged' => $message->isAcknowledged(),
                ];
            });

        return response()->json([
            'messages' => $messages,
            'total' => $messages->count(),
        ]);
    }
}
