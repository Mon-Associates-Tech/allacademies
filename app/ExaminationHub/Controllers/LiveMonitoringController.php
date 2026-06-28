<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\ExamAdminMessage;
use App\ExaminationHub\Models\ExamReadmissionGrant;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
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

    public function index(GeneralExam $exam): View
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getExamMonitoringData($exam);

        return view('examination-hub.live-monitoring.index', [
            'exam'        => $exam,
            'initialData' => $data,
        ]);
    }

    public function show(GeneralExam $exam, int $submissionId): View
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getParticipantLiveData($submissionId);

        abort_if(! $data, 404, 'Participant session not found.');

        return view('examination-hub.live-monitoring.show', [
            'exam'        => $exam,
            'participant' => $data['participant'],
            'violations'  => $data['violations'],
        ]);
    }

    // ─── API Endpoints ───────────────────────────────────────────────────────

    public function apiParticipants(GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        return response()->json($this->monitoringService->getExamMonitoringData($exam));
    }

    public function apiParticipant(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $this->monitoringService->getParticipantLiveData($submissionId);

        return $data
            ? response()->json($data)
            : response()->json(['error' => 'Participant not found'], 404);
    }

    // ─── Messaging ───────────────────────────────────────────────────────────

    public function warn(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate(['message' => ['required', 'string', 'max:500']]);

        $heartbeat = $this->monitoringService->sendWarning($submissionId, $data['message']);

        return $heartbeat
            ? response()->json(['status' => 'warning_sent', 'message' => $data['message']])
            : response()->json(['error' => 'Participant not found'], 404);
    }

    public function message(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate(['message' => ['required', 'string', 'max:500']]);

        $heartbeat = $this->monitoringService->sendMessage($submissionId, $data['message']);

        return $heartbeat
            ? response()->json(['status' => 'message_sent', 'message' => $data['message']])
            : response()->json(['error' => 'Participant not found'], 404);
    }

    public function messageAll(Request $request, GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data   = $request->validate(['message' => ['required', 'string', 'max:500']]);
        $result = $this->monitoringService->sendMessageToAllActiveParticipants($exam, $data['message']);

        return response()->json([
            'status'       => 'messages_sent',
            'sent_count'   => $result['sent_count'],
            'failed_count' => $result['failed_count'],
        ]);
    }

    // ─── Session Control ──────────────────────────────────────────────────────

    public function terminate(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $heartbeat = $this->monitoringService->terminateParticipant(
            $submissionId, auth()->id(), $data['reason']
        );

        return $heartbeat
            ? response()->json(['status' => 'terminated', 'reason' => $data['reason']])
            : response()->json(['error' => 'Participant not found'], 404);
    }

    public function forceSubmit(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $heartbeat = $this->monitoringService->forceSubmit(
            $submissionId, auth()->id(), $data['reason'] ?? 'Admin forced submission'
        );

        return $heartbeat
            ? response()->json(['status' => 'force_submitted'])
            : response()->json(['error' => 'Participant not found'], 404);
    }

    public function clearWarning(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $heartbeat = $this->monitoringService->clearWarning($submissionId);

        return $heartbeat
            ? response()->json(['status' => 'warning_cleared'])
            : response()->json(['error' => 'Participant not found'], 404);
    }

    // ─── Time Extension ───────────────────────────────────────────────────────

    /**
     * POST /exams/{exam}/live-monitoring/extend-time/{submission}
     * Extend time for a single participant.
     */
    public function extendTime(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'minutes' => ['required', 'integer', 'min:1', 'max:480'],
        ]);

        $submission = GeneralExamSubmission::where('id', $submissionId)
            ->where('general_exam_id', $exam->id)
            ->first();

        if (! $submission) {
            return response()->json(['error' => 'Submission not found'], 404);
        }

        $submission->extendTime($data['minutes'], auth()->id());

        // Also notify the heartbeat layer so the running timer picks up the change
        $this->monitoringService->extendTime($submissionId, $data['minutes']);

        return response()->json([
            'status'              => 'time_extended',
            'added_minutes'       => $data['minutes'],
            'total_extra_minutes' => $submission->fresh()->extra_time_minutes,
        ]);
    }

    /**
     * POST /exams/{exam}/live-monitoring/extend-time-group
     * Extend time for a hand-picked subset of participants.
     *
     * Body: { submission_ids: [1, 2, 3], minutes: 20 }
     */
    public function extendTimeGroup(Request $request, GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'submission_ids'   => ['required', 'array', 'min:1', 'max:500'],
            'submission_ids.*' => ['integer'],
            'minutes'          => ['required', 'integer', 'min:1', 'max:480'],
        ]);

        $submissions = GeneralExamSubmission::whereIn('id', $data['submission_ids'])
            ->where('general_exam_id', $exam->id)
            ->get();

        if ($submissions->isEmpty()) {
            return response()->json(['error' => 'No matching submissions found'], 404);
        }

        $updatedIds = [];
        foreach ($submissions as $submission) {
            $submission->extendTime($data['minutes'], auth()->id());
            $this->monitoringService->extendTime($submission->id, $data['minutes']);
            $updatedIds[] = $submission->id;
        }

        return response()->json([
            'status'          => 'time_extended',
            'added_minutes'   => $data['minutes'],
            'updated_count'   => count($updatedIds),
            'updated_ids'     => $updatedIds,
        ]);
    }

    /**
     * POST /exams/{exam}/live-monitoring/extend-time-all
     * Extend time for every participant that is still in progress.
     *
     * Body: { minutes: 20 }
     */
    public function extendTimeAll(Request $request, GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'minutes' => ['required', 'integer', 'min:1', 'max:480'],
        ]);

        // Only extend for participants who are actively sitting the exam
        $submissions = GeneralExamSubmission::where('general_exam_id', $exam->id)
            ->whereIn('status', [
                GeneralExamSubmission::STATUS_IN_PROGRESS,
                GeneralExamSubmission::STATUS_NOT_STARTED,
            ])
            ->whereNull('submitted_at')
            ->get();

        foreach ($submissions as $submission) {
            $submission->extendTime($data['minutes'], auth()->id());
            $this->monitoringService->extendTime($submission->id, $data['minutes']);
        }

        return response()->json([
            'status'        => 'time_extended_all',
            'added_minutes' => $data['minutes'],
            'updated_count' => $submissions->count(),
        ]);
    }

    // ─── Re-admission ─────────────────────────────────────────────────────────

    /**
     * POST /exams/{exam}/live-monitoring/readmit/{submission}
     * Grant a candidate the ability to re-enter an exam that they have already
     * submitted (or whose session was terminated).
     *
     * Body:
     *   mode    — 'continue' (resume old answers) | 'fresh' (start over)
     *   reason  — optional admin note
     *   minutes — optional extra time to grant alongside the readmission
     *   expires_at — ISO-8601 timestamp after which the grant lapses (optional)
     */
    public function grantReadmission(Request $request, GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'mode'       => ['required', 'in:continue,fresh'],
            'reason'     => ['nullable', 'string', 'max:1000'],
            'minutes'    => ['nullable', 'integer', 'min:1', 'max:480'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $submission = GeneralExamSubmission::where('id', $submissionId)
            ->where('general_exam_id', $exam->id)
            ->first();

        if (! $submission) {
            return response()->json(['error' => 'Submission not found'], 404);
        }

        // Revoke any existing unused grant so there is never more than one active
        ExamReadmissionGrant::active()
            ->where('original_submission_id', $submissionId)
            ->each(fn ($g) => $g->revoke(auth()->id(), 'Superseded by new grant'));

        $grant = ExamReadmissionGrant::create([
            'general_exam_id'       => $exam->id,
            'original_submission_id' => $submissionId,
            'granted_by'            => auth()->id(),
            'mode'                  => $data['mode'],
            'reason'                => $data['reason'] ?? null,
            'expires_at'            => $data['expires_at'] ?? null,
        ]);

        // Optionally give the candidate extra time alongside the readmission
        if (! empty($data['minutes'])) {
            $submission->extendTime($data['minutes'], auth()->id());
        }

        return response()->json([
            'status'     => 'readmission_granted',
            'grant_id'   => $grant->id,
            'mode'       => $grant->mode,
            'expires_at' => $grant->expires_at?->toIso8601String(),
        ], 201);
    }

    /**
     * DELETE /exams/{exam}/live-monitoring/readmit/{grant}
     * Revoke an unused readmission grant.
     */
    public function revokeReadmission(Request $request, GeneralExam $exam, int $grantId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $grant = ExamReadmissionGrant::where('id', $grantId)
            ->where('general_exam_id', $exam->id)
            ->first();

        if (! $grant) {
            return response()->json(['error' => 'Grant not found'], 404);
        }

        if ($grant->isUsed()) {
            return response()->json(['error' => 'Grant has already been used and cannot be revoked'], 409);
        }

        $grant->revoke(auth()->id(), $data['reason'] ?? '');

        return response()->json(['status' => 'grant_revoked']);
    }

    /**
     * GET /exams/{exam}/live-monitoring/readmissions
     * List active (pending) readmission grants for the exam.
     * Useful for the admin to see who is waiting to re-enter.
     */
    public function listReadmissions(GeneralExam $exam): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $grants = ExamReadmissionGrant::active()
            ->where('general_exam_id', $exam->id)
            ->with(['originalSubmission', 'grantedBy'])
            ->get()
            ->map(fn ($g) => [
                'grant_id'           => $g->id,
                'mode'               => $g->mode,
                'reason'             => $g->reason,
                'expires_at'         => $g->expires_at?->toIso8601String(),
                'created_at'         => $g->created_at->toIso8601String(),
                'granted_by'         => $g->grantedBy?->name,
                'submission_id'      => $g->original_submission_id,
                'participant_name'   => $g->originalSubmission?->participant_name,
                'participant_email'  => $g->originalSubmission?->participant_email,
            ]);

        return response()->json(['grants' => $grants, 'total' => $grants->count()]);
    }

    // ─── Message History ─────────────────────────────────────────────────────

    public function getMessageHistory(GeneralExam $exam, int $submissionId): JsonResponse
    {
        $this->ensureOwnerAccess($exam);

        $messages = ExamAdminMessage::forSubmission($submissionId)
            ->with('sender')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($message) => [
                'id'              => $message->id,
                'message_type'    => $message->message_type,
                'type_label'      => $message->getTypeLabel(),
                'message'         => $message->message,
                'metadata'        => $message->metadata,
                'sent_by'         => $message->sender ? [
                    'id'    => $message->sender->id,
                    'name'  => $message->sender->name,
                    'email' => $message->sender->email,
                ] : null,
                'sent_at'         => $message->created_at->toIso8601String(),
                'delivered_at'    => $message->delivered_at?->toIso8601String(),
                'acknowledged_at' => $message->acknowledged_at?->toIso8601String(),
                'is_delivered'    => $message->isDelivered(),
                'is_acknowledged' => $message->isAcknowledged(),
            ]);

        return response()->json(['messages' => $messages, 'total' => $messages->count()]);
    }
}