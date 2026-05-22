<?php

namespace App\ExaminationHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ExamParticipantHeartbeat extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_ACTIVE = 'active';

    public const STATUS_IDLE = 'idle';

    public const STATUS_AWAY = 'away';

    public const STATUS_DISCONNECTED = 'disconnected';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_TERMINATED = 'terminated';

    // Timeout thresholds (in seconds)
    public const IDLE_THRESHOLD = 60;   // 1 minute without activity

    public const AWAY_THRESHOLD = 180;  // 3 minutes

    public const DISCONNECTED_THRESHOLD = 300;  // 5 minutes

    protected $table = 'examhub_participant_heartbeats';

    protected $fillable = [
        'general_exam_id',
        'general_exam_submission_id',
        'participant_name',
        'participant_email',
        'session_token',
        'last_heartbeat_at',
        'started_at',
        'status',
        'is_focused',
        'current_question_index',
        'current_section_index',
        'questions_answered',
        'total_questions',
        'violation_count',
        'high_severity_count',
        'medium_severity_count',
        'is_flagged',
        'ip_address',
        'user_agent',
        'browser',
        'os',
        'has_warning',
        'admin_message',
        'warned_at',
        'terminated_by',
        'terminated_at',
        'termination_reason',
    ];

    protected function casts(): array
    {
        return [
            'last_heartbeat_at' => 'datetime',
            'started_at' => 'datetime',
            'warned_at' => 'datetime',
            'terminated_at' => 'datetime',
            'is_focused' => 'boolean',
            'is_flagged' => 'boolean',
            'has_warning' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function exam(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'general_exam_submission_id');
    }

    public function terminatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terminated_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForExam($query, int $examId)
    {
        return $query->where('general_exam_id', $examId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_IDLE, self::STATUS_AWAY]);
    }

    public function scopeOnline($query)
    {
        return $query->where('last_heartbeat_at', '>=', now()->subSeconds(self::DISCONNECTED_THRESHOLD));
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    // ─── Static Helpers ──────────────────────────────────────────────────────

    public static function generateSessionToken(): string
    {
        return Str::random(64);
    }

    public static function createForSubmission(GeneralExamSubmission $submission, array $deviceInfo = []): self
    {
        $exam = $submission->assignment;

        return self::updateOrCreate(
            ['general_exam_submission_id' => $submission->id],
            [
                'general_exam_id' => $exam->id,
                'participant_name' => $submission->participant_name,
                'participant_email' => $submission->participant_email,
                'session_token' => self::generateSessionToken(),
                'started_at' => now(),
                'last_heartbeat_at' => now(),
                'status' => self::STATUS_ACTIVE,
                'total_questions' => $exam->questions()->count(),
                'ip_address' => $deviceInfo['ip'] ?? request()->ip(),
                'user_agent' => $deviceInfo['user_agent'] ?? request()->userAgent(),
                'browser' => $deviceInfo['browser'] ?? null,
                'os' => $deviceInfo['os'] ?? null,
            ]
        );
    }

    // ─── Instance Methods ────────────────────────────────────────────────────

    /**
     * Update heartbeat and recalculate status based on activity.
     */
    public function recordHeartbeat(array $data = []): self
    {
        $updateData = [
            'last_heartbeat_at' => now(),
            'status' => self::STATUS_ACTIVE,
        ];

        if (isset($data['is_focused'])) {
            $updateData['is_focused'] = $data['is_focused'];
        }

        if (isset($data['current_question_index'])) {
            $updateData['current_question_index'] = $data['current_question_index'];
        }

        if (isset($data['current_section_index'])) {
            $updateData['current_section_index'] = $data['current_section_index'];
        }

        if (isset($data['questions_answered'])) {
            $updateData['questions_answered'] = $data['questions_answered'];
        }

        $this->update($updateData);
        $this->refresh();

        return $this;
    }

    /**
     * Calculate current status based on last heartbeat time.
     */
    public function calculateStatus(): string
    {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_TERMINATED) {
            return $this->status;
        }

        if (! $this->last_heartbeat_at) {
            return self::STATUS_DISCONNECTED;
        }

        $secondsSinceLastHeartbeat = max(0, (int) $this->last_heartbeat_at->diffInSeconds(now()));

        return match (true) {
            $secondsSinceLastHeartbeat >= self::DISCONNECTED_THRESHOLD => self::STATUS_DISCONNECTED,
            $secondsSinceLastHeartbeat >= self::AWAY_THRESHOLD => self::STATUS_AWAY,
            $secondsSinceLastHeartbeat >= self::IDLE_THRESHOLD => self::STATUS_IDLE,
            default => self::STATUS_ACTIVE,
        };
    }

    /**
     * Sync violation counts from proctoring logs.
     */
    public function syncViolationCounts(): self
    {
        $logs = ExamProctoringLog::forSubmission($this->general_exam_submission_id)->get();

        $this->update([
            'violation_count' => $logs->count(),
            'high_severity_count' => $logs->where('severity', ExamProctoringLog::SEVERITY_HIGH)->count(),
            'medium_severity_count' => $logs->where('severity', ExamProctoringLog::SEVERITY_MEDIUM)->count(),
            'is_flagged' => $this->high_severity_count >= 3 || $this->medium_severity_count >= 5,
        ]);

        return $this->refresh();
    }

    /**
     * Mark session as completed.
     */
    public function markCompleted(): self
    {
        $this->update(['status' => self::STATUS_COMPLETED]);

        return $this;
    }

    /**
     * Terminate session (admin action).
     */
    public function terminate(int $adminId, string $reason): self
    {
        $this->update([
            'status' => self::STATUS_TERMINATED,
            'terminated_by' => $adminId,
            'terminated_at' => now(),
            'termination_reason' => $reason,
        ]);

        return $this;
    }

    /**
     * Send warning to participant.
     */
    public function sendWarning(string $message): self
    {
        $this->update([
            'has_warning' => true,
            'admin_message' => $message,
            'warned_at' => now(),
        ]);

        return $this;
    }

    /**
     * Clear warning after participant acknowledges.
     */
    public function clearWarning(): self
    {
        $this->update([
            'has_warning' => false,
            'admin_message' => null,
        ]);

        return $this;
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentage(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return round(($this->questions_answered / $this->total_questions) * 100, 1);
    }

    /**
     * Get time elapsed since start.
     */
    public function getElapsedTime(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        return max(0, (int) $this->started_at->diffInSeconds(now()));
    }

    /**
     * Convert to array for broadcasting/API response.
     */
    public function toLiveData(?int $examDurationMinutes = null): array
    {
        $elapsedSeconds = $this->getElapsedTime();

        $remainingSeconds = null;
        if ($examDurationMinutes && $this->started_at) {
            $endAt = $this->started_at->copy()->addMinutes($examDurationMinutes);
            $remainingSeconds = max(0, (int) $endAt->diffInSeconds(now(), false));
        }

        return [
            'id' => $this->id,
            'submission_id' => $this->general_exam_submission_id,
            'participant_name' => $this->participant_name ?? 'Anonymous',
            'participant_email' => $this->participant_email,
            'session_token' => $this->session_token,
            'status' => $this->status,
            'is_focused' => $this->is_focused,
            'current_question' => $this->current_question_index + 1,
            'current_section' => $this->current_section_index + 1,
            'questions_answered' => $this->questions_answered,
            'total_questions' => $this->total_questions,
            'progress_percentage' => $this->getProgressPercentage(),
            'violation_count' => $this->violation_count,
            'high_severity_count' => $this->high_severity_count,
            'medium_severity_count' => $this->medium_severity_count,
            'is_flagged' => $this->is_flagged,
            'has_warning' => $this->has_warning,
            'admin_message' => $this->admin_message,
            'started_at' => $this->started_at?->toIso8601String(),
            'last_heartbeat_at' => $this->last_heartbeat_at?->toIso8601String(),
            'elapsed_seconds' => $elapsedSeconds,
            'remaining_seconds' => $remainingSeconds,
            'has_duration' => $examDurationMinutes !== null && $examDurationMinutes > 0,
            'ip_address' => $this->ip_address,
            'browser' => $this->browser,
            'os' => $this->os,
        ];
    }
}
