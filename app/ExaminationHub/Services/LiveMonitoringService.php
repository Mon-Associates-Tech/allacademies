<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Events\AdminActionSent;
use App\ExaminationHub\Events\ParticipantHeartbeatReceived;
use App\ExaminationHub\Events\ParticipantStatusChanged;
use App\ExaminationHub\Events\ParticipantViolationRecorded;
use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\ExamProctoringLog;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Support\Facades\DB;

class LiveMonitoringService
{
    public function __construct(
        private readonly ProctoringService $proctoringService
    ) {}

    // ─── Session Management ──────────────────────────────────────────────────

    /**
     * Initialize or retrieve heartbeat session for a submission.
     */
    public function initializeSession(GeneralExamSubmission $submission, array $deviceInfo = []): ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::createForSubmission($submission, $deviceInfo);

        broadcast(new ParticipantHeartbeatReceived($heartbeat))->toOthers();

        return $heartbeat;
    }

    /**
     * Process incoming heartbeat from participant.
     */
    public function processHeartbeat(string $sessionToken, array $data = []): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('session_token', $sessionToken)->first();

        if (! $heartbeat) {
            return null;
        }

        // Check if terminated
        if ($heartbeat->status === ExamParticipantHeartbeat::STATUS_TERMINATED) {
            return $heartbeat;
        }

        $previousStatus = $heartbeat->calculateStatus();
        $heartbeat->recordHeartbeat($data);
        $newStatus = $heartbeat->calculateStatus();

        // Broadcast heartbeat update
        broadcast(new ParticipantHeartbeatReceived($heartbeat))->toOthers();

        // Broadcast status change if different
        if ($previousStatus !== $newStatus) {
            broadcast(new ParticipantStatusChanged($heartbeat, $previousStatus, $newStatus))->toOthers();
        }

        return $heartbeat;
    }

    /**
     * Record a violation and update heartbeat counters.
     */
    public function recordViolation(
        GeneralExamSubmission $submission,
        string $eventType,
        array $eventData = []
    ): ExamProctoringLog {
        // Log the proctoring event
        $log = $this->proctoringService->logEvent($submission, $eventType, $eventData);

        // Update heartbeat counters
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submission->id)->first();

        if ($heartbeat) {
            $heartbeat->syncViolationCounts();

            // Broadcast violation event
            broadcast(new ParticipantViolationRecorded($heartbeat, $log))->toOthers();
        }

        return $log;
    }

    // ─── Admin Dashboard Data ────────────────────────────────────────────────

    /**
     * Get live monitoring data for an exam.
     */
    public function getExamMonitoringData(GeneralExam $exam): array
    {
        $heartbeats = ExamParticipantHeartbeat::forExam($exam->id)
            ->with('submission')
            ->get();

        // Sync status for each heartbeat against ground truth (submission state + timing)
        $heartbeats->each(function ($heartbeat) {
            $terminal = in_array($heartbeat->status, [
                ExamParticipantHeartbeat::STATUS_COMPLETED,
                ExamParticipantHeartbeat::STATUS_TERMINATED,
            ]);

            if ($terminal) {
                return;
            }

            // Submission already submitted → mark completed regardless of heartbeat timing
            if ($heartbeat->submission?->isSubmitted()) {
                $heartbeat->update(['status' => ExamParticipantHeartbeat::STATUS_COMPLETED]);
                $heartbeat->status = ExamParticipantHeartbeat::STATUS_COMPLETED;

                return;
            }

            // No submission started yet (participant never actually entered) → disconnected
            if (! $heartbeat->submission || ! $heartbeat->submission->started_at) {
                $heartbeat->update(['status' => ExamParticipantHeartbeat::STATUS_DISCONNECTED]);
                $heartbeat->status = ExamParticipantHeartbeat::STATUS_DISCONNECTED;

                return;
            }

            // Normal timing-based status
            $currentStatus = $heartbeat->calculateStatus();
            if ($heartbeat->status !== $currentStatus) {
                $heartbeat->update(['status' => $currentStatus]);
                $heartbeat->status = $currentStatus;
            }
        });

        $durationMinutes = $exam->duration_in_minutes;

        $participants = $heartbeats->map(fn ($h) => $h->toLiveData($durationMinutes));

        // Calculate stats
        $stats = [
            'total_participants' => $heartbeats->count(),
            'active' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_ACTIVE)->count(),
            'idle' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_IDLE)->count(),
            'away' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_AWAY)->count(),
            'disconnected' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_DISCONNECTED)->count(),
            'completed' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_COMPLETED)->count(),
            'terminated' => $heartbeats->where('status', ExamParticipantHeartbeat::STATUS_TERMINATED)->count(),
            'flagged' => $heartbeats->where('is_flagged', true)->count(),
            'total_violations' => $heartbeats->sum('violation_count'),
            'high_violations' => $heartbeats->sum('high_severity_count'),
            'medium_violations' => $heartbeats->sum('medium_severity_count'),
        ];

        return [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'duration_minutes' => $exam->duration_in_minutes,
                'total_questions' => $exam->questions()->count(),
                'proctoring_enabled' => $exam->proctoring_enabled,
                'starts_at' => $exam->starts_at?->toIso8601String(),
                'ends_at' => $exam->ends_at?->toIso8601String(),
            ],
            'stats' => $stats,
            'participants' => $participants->values()->toArray(),
        ];
    }

    /**
     * Get single participant's detailed live data.
     */
    public function getParticipantLiveData(int $submissionId): ?array
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)
            ->with('submission')
            ->first();

        if (! $heartbeat) {
            return null;
        }

        $logs = ExamProctoringLog::forSubmission($submissionId)
            ->orderByDesc('occurred_at')
            ->limit(50)
            ->get();

        return [
            'participant' => $heartbeat->toLiveData($heartbeat->exam?->duration_in_minutes),
            'violations' => $logs->map(fn ($log) => [
                'id' => $log->id,
                'event_type' => $log->event_type,
                'severity' => $log->severity,
                'event_data' => $log->event_data,
                'occurred_at' => $log->occurred_at->toIso8601String(),
            ])->toArray(),
        ];
    }

    // ─── Admin Actions ───────────────────────────────────────────────────────

    /**
     * Send warning to participant.
     */
    public function sendWarning(int $submissionId, string $message): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        $heartbeat->sendWarning($message);

        broadcast(new AdminActionSent($heartbeat, 'warning', $message))->toOthers();

        return $heartbeat;
    }

    /**
     * Terminate participant session.
     */
    public function terminateParticipant(int $submissionId, int $adminId, string $reason): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        $heartbeat->terminate($adminId, $reason);

        // Also mark the submission as auto-submitted due to termination
        $submission = $heartbeat->submission;
        if ($submission && ! $submission->submitted_at) {
            $submission->submit(autoSubmitted: true, reason: "Terminated by admin: {$reason}");
        }

        broadcast(new AdminActionSent($heartbeat, 'terminate', $reason))->toOthers();

        return $heartbeat;
    }

    /**
     * Force submit participant's exam.
     */
    public function forceSubmit(int $submissionId, int $adminId, string $reason): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        $submission = $heartbeat->submission;

        if ($submission && ! $submission->submitted_at) {
            DB::transaction(function () use ($submission, $heartbeat, $reason) {
                $submission->submit(autoSubmitted: true, reason: "Force submitted by admin: {$reason}");
                $submission->gradeSubmission();
                $heartbeat->markCompleted();
            });
        }

        broadcast(new AdminActionSent($heartbeat, 'force_submit', $reason))->toOthers();

        return $heartbeat;
    }

    /**
     * Send a message to participant (non-warning).
     */
    public function sendMessage(int $submissionId, string $message): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        // Persist message so it's delivered via next heartbeat poll
        $heartbeat->update(['admin_message' => $message]);

        broadcast(new AdminActionSent($heartbeat, 'message', $message))->toOthers();

        return $heartbeat;
    }

    /**
     * Extend time for a participant.
     */
    public function extendTime(int $submissionId, int $additionalMinutes): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        // You may need to store extended time in submission or a separate field
        // For now, we'll broadcast the extension

        broadcast(new AdminActionSent(
            $heartbeat,
            'extend_time',
            "Your exam time has been extended by {$additionalMinutes} minutes.",
            ['additional_minutes' => $additionalMinutes]
        ))->toOthers();

        return $heartbeat;
    }

    /**
     * Clear warning for participant.
     */
    public function clearWarning(int $submissionId): ?ExamParticipantHeartbeat
    {
        $heartbeat = ExamParticipantHeartbeat::where('general_exam_submission_id', $submissionId)->first();

        if (! $heartbeat) {
            return null;
        }

        $heartbeat->clearWarning();

        return $heartbeat;
    }

    // ─── Cleanup ─────────────────────────────────────────────────────────────

    /**
     * Update statuses for all active heartbeats (can be run via scheduler).
     */
    public function refreshAllStatuses(): int
    {
        $updated = 0;

        ExamParticipantHeartbeat::active()
            ->chunk(100, function ($heartbeats) use (&$updated) {
                foreach ($heartbeats as $heartbeat) {
                    $newStatus = $heartbeat->calculateStatus();
                    if ($heartbeat->status !== $newStatus) {
                        $oldStatus = $heartbeat->status;
                        $heartbeat->update(['status' => $newStatus]);
                        broadcast(new ParticipantStatusChanged($heartbeat, $oldStatus, $newStatus))->toOthers();
                        $updated++;
                    }
                }
            });

        return $updated;
    }
}
