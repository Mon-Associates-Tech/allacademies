<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamProctoringLog extends Model
{
    use HasFactory;

    // Severity constants
    public const SEVERITY_LOW    = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH   = 'high';

    // Known event types
    public const EVENT_TAB_SWITCH       = 'tab_switch';
    public const EVENT_WINDOW_BLUR      = 'window_blur';
    public const EVENT_COPY_ATTEMPT     = 'copy_attempt';
    public const EVENT_PASTE_ATTEMPT    = 'paste_attempt';
    public const EVENT_RIGHT_CLICK      = 'right_click';
    public const EVENT_KEYBOARD_SHORTCUT = 'keyboard_shortcut';
    public const EVENT_FULLSCREEN_EXIT  = 'fullscreen_exit';
    public const EVENT_EXAM_EXIT        = 'exam_exit';
    public const EVENT_MULTIPLE_FACES   = 'multiple_faces';
    public const EVENT_NO_FACE          = 'no_face';
    public const EVENT_FACE_MISMATCH    = 'face_mismatch';

    protected $fillable = [
        'general_exam_submission_id',
        'event_type',
        'event_data',
        'severity',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'event_data'  => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function submission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'general_exam_submission_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeHighSeverity($query)
    {
        return $query->where('severity', self::SEVERITY_HIGH);
    }

    public function scopeForSubmission($query, int $submissionId)
    {
        return $query->where('general_exam_submission_id', $submissionId);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Map event types to their default severity levels.
     */
    public static function defaultSeverity(string $eventType): string
    {
        return match ($eventType) {
            self::EVENT_TAB_SWITCH, self::EVENT_WINDOW_BLUR, self::EVENT_FULLSCREEN_EXIT => self::SEVERITY_MEDIUM,
            self::EVENT_COPY_ATTEMPT, self::EVENT_PASTE_ATTEMPT, self::EVENT_KEYBOARD_SHORTCUT => self::SEVERITY_LOW,
            self::EVENT_RIGHT_CLICK => self::SEVERITY_LOW,
            self::EVENT_EXAM_EXIT, self::EVENT_MULTIPLE_FACES, self::EVENT_FACE_MISMATCH => self::SEVERITY_HIGH,
            self::EVENT_NO_FACE => self::SEVERITY_MEDIUM,
            default => self::SEVERITY_LOW,
        };
    }

    public function isHighSeverity(): bool
    {
        return $this->severity === self::SEVERITY_HIGH;
    }
}
