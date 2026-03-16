<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProctoringSession extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_TERMINATED = 'terminated';

    public const STATUS_INVALID = 'invalid';

    public const EVENT_SESSION_START = 'session_start';

    public const EVENT_SESSION_END = 'session_end';

    public const EVENT_TAB_SWITCH = 'tab_switch';

    public const EVENT_FULLSCREEN_EXIT = 'fullscreen_exit';

    public const EVENT_FULLSCREEN_ENTER = 'fullscreen_enter';

    public const EVENT_WEBCAM_DISCONNECT = 'webcam_disconnect';

    public const EVENT_WEBCAM_CONNECT = 'webcam_connect';

    public const EVENT_COPY_ATTEMPT = 'copy_attempt';

    public const EVENT_PASTE_ATTEMPT = 'paste_attempt';

    public const EVENT_RIGHT_CLICK = 'right_click';

    public const EVENT_SNAPSHOT_TAKEN = 'snapshot_taken';

    public const EVENT_WARNING_ISSUED = 'warning_issued';

    protected $fillable = [
        'general_exam_submission_id',
        'started_at',
        'ended_at',
        'status',
        'webcam_enabled',
        'fullscreen_enabled',
        'browser_locked',
        'tab_switches',
        'fullscreen_exits',
        'webcam_disconnects',
        'copy_paste_attempts',
        'right_click_attempts',
        'events',
        'snapshots',
        'browser',
        'os',
        'screen_resolution',
        'ip_address',
        'is_valid',
        'invalidation_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'events' => 'array',
            'snapshots' => 'array',
            'webcam_enabled' => 'boolean',
            'fullscreen_enabled' => 'boolean',
            'browser_locked' => 'boolean',
            'is_valid' => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'general_exam_submission_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function start(array $environmentInfo = []): void
    {
        $this->update([
            'started_at' => now(),
            'status' => self::STATUS_ACTIVE,
            'browser' => $environmentInfo['browser'] ?? null,
            'os' => $environmentInfo['os'] ?? null,
            'screen_resolution' => $environmentInfo['screen_resolution'] ?? null,
            'ip_address' => $environmentInfo['ip_address'] ?? null,
        ]);

        $this->logEvent(self::EVENT_SESSION_START, $environmentInfo);
    }

    public function end(): void
    {
        $this->update([
            'ended_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ]);

        $this->logEvent(self::EVENT_SESSION_END);
    }

    public function terminate(string $reason): void
    {
        $this->update([
            'ended_at' => now(),
            'status' => self::STATUS_TERMINATED,
            'is_valid' => false,
            'invalidation_reason' => $reason,
        ]);

        $this->logEvent(self::EVENT_SESSION_END, ['reason' => $reason, 'terminated' => true]);
    }

    public function logEvent(string $type, ?array $details = null): void
    {
        $events = $this->events ?? [];
        $events[] = [
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'details' => $details,
        ];

        $this->update(['events' => $events]);
    }

    public function recordTabSwitch(): int
    {
        $newCount = $this->tab_switches + 1;
        $this->update(['tab_switches' => $newCount]);
        $this->logEvent(self::EVENT_TAB_SWITCH, ['count' => $newCount]);

        return $newCount;
    }

    public function recordFullscreenExit(): int
    {
        $newCount = $this->fullscreen_exits + 1;
        $this->update([
            'fullscreen_exits' => $newCount,
            'fullscreen_enabled' => false,
        ]);
        $this->logEvent(self::EVENT_FULLSCREEN_EXIT, ['count' => $newCount]);

        return $newCount;
    }

    public function recordFullscreenEnter(): void
    {
        $this->update(['fullscreen_enabled' => true]);
        $this->logEvent(self::EVENT_FULLSCREEN_ENTER);
    }

    public function recordWebcamDisconnect(): int
    {
        $newCount = $this->webcam_disconnects + 1;
        $this->update([
            'webcam_disconnects' => $newCount,
            'webcam_enabled' => false,
        ]);
        $this->logEvent(self::EVENT_WEBCAM_DISCONNECT, ['count' => $newCount]);

        return $newCount;
    }

    public function recordWebcamConnect(): void
    {
        $this->update(['webcam_enabled' => true]);
        $this->logEvent(self::EVENT_WEBCAM_CONNECT);
    }

    public function recordCopyPasteAttempt(string $action): int
    {
        $newCount = $this->copy_paste_attempts + 1;
        $this->update(['copy_paste_attempts' => $newCount]);
        $this->logEvent($action === 'copy' ? self::EVENT_COPY_ATTEMPT : self::EVENT_PASTE_ATTEMPT, ['count' => $newCount]);

        return $newCount;
    }

    public function recordRightClick(): int
    {
        $newCount = $this->right_click_attempts + 1;
        $this->update(['right_click_attempts' => $newCount]);
        $this->logEvent(self::EVENT_RIGHT_CLICK, ['count' => $newCount]);

        return $newCount;
    }

    public function addSnapshot(string $path): void
    {
        $snapshots = $this->snapshots ?? [];
        $snapshots[] = [
            'path' => $path,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['snapshots' => $snapshots]);
        $this->logEvent(self::EVENT_SNAPSHOT_TAKEN, ['path' => $path]);
    }

    public function issueWarning(string $reason): void
    {
        $this->logEvent(self::EVENT_WARNING_ISSUED, ['reason' => $reason]);
    }

    public function getTotalViolations(): int
    {
        return $this->tab_switches
            + $this->fullscreen_exits
            + $this->webcam_disconnects
            + $this->copy_paste_attempts
            + $this->right_click_attempts;
    }

    public function hasExceededTabSwitchLimit(int $limit): bool
    {
        return $this->tab_switches >= $limit;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function markAsInvalid(string $reason, ?int $reviewerId = null): void
    {
        $this->update([
            'status' => self::STATUS_INVALID,
            'is_valid' => false,
            'invalidation_reason' => $reason,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => $reviewerId ? now() : null,
        ]);
    }

    public function markAsValid(?int $reviewerId = null): void
    {
        $this->update([
            'is_valid' => true,
            'invalidation_reason' => null,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => $reviewerId ? now() : null,
        ]);
    }

    public function getViolationSummary(): array
    {
        return [
            'tab_switches' => $this->tab_switches,
            'fullscreen_exits' => $this->fullscreen_exits,
            'webcam_disconnects' => $this->webcam_disconnects,
            'copy_paste_attempts' => $this->copy_paste_attempts,
            'right_click_attempts' => $this->right_click_attempts,
            'total' => $this->getTotalViolations(),
        ];
    }

    public function getDuration(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        $endTime = $this->ended_at ?? now();

        return $this->started_at->diffInSeconds($endTime);
    }
}
