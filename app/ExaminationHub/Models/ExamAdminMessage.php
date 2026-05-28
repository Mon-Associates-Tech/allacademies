<?php

namespace App\ExaminationHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAdminMessage extends Model
{
    use HasFactory;

    // Message type constants
    public const TYPE_WARNING = 'warning';
    public const TYPE_INFO = 'info';
    public const TYPE_TERMINATION = 'termination';
    public const TYPE_FORCE_SUBMIT = 'force_submit';
    public const TYPE_TIME_EXTENSION = 'time_extension';

    protected $table = 'exam_admin_messages';

    protected $fillable = [
        'general_exam_id',
        'general_exam_submission_id',
        'sent_by',
        'message_type',
        'message',
        'metadata',
        'delivered_at',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'delivered_at' => 'datetime',
            'acknowledged_at' => 'datetime',
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

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForExam($query, int $examId)
    {
        return $query->where('general_exam_id', $examId);
    }

    public function scopeForSubmission($query, int $submissionId)
    {
        return $query->where('general_exam_submission_id', $submissionId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeUndelivered($query)
    {
        return $query->whereNull('delivered_at');
    }

    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    // ─── Static Helpers ──────────────────────────────────────────────────────

    /**
     * Create a new admin message.
     */
    public static function createMessage(
        GeneralExamSubmission $submission,
        User $sender,
        string $type,
        string $message,
        array $metadata = []
    ): self {
        return self::create([
            'general_exam_id' => $submission->assignment->id,
            'general_exam_submission_id' => $submission->id,
            'sent_by' => $sender->id,
            'message_type' => $type,
            'message' => $message,
            'metadata' => !empty($metadata) ? $metadata : null,
        ]);
    }

    // ─── Instance Methods ────────────────────────────────────────────────────

    /**
     * Mark message as delivered.
     */
    public function markDelivered(): self
    {
        $this->update(['delivered_at' => now()]);
        
        return $this;
    }

    /**
     * Mark message as acknowledged by participant.
     */
    public function markAcknowledged(): self
    {
        $this->update(['acknowledged_at' => now()]);
        
        return $this;
    }

    /**
     * Check if message has been delivered.
     */
    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }

    /**
     * Check if message has been acknowledged.
     */
    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    /**
     * Get formatted timestamp for display.
     */
    public function getFormattedTimestamp(): string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    /**
     * Get human-readable message type label.
     */
    public function getTypeLabel(): string
    {
        return match ($this->message_type) {
            self::TYPE_WARNING => 'Warning',
            self::TYPE_INFO => 'Information',
            self::TYPE_TERMINATION => 'Termination',
            self::TYPE_FORCE_SUBMIT => 'Force Submit',
            self::TYPE_TIME_EXTENSION => 'Time Extension',
            default => ucfirst($this->message_type),
        };
    }
}
