<?php

namespace App\ExaminationHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamReadmissionGrant extends Model
{
    protected $fillable = [
        'general_exam_id',
        'original_submission_id',
        'granted_by',
        'mode',
        'reason',
        'used_at',
        'new_submission_id',
        'expires_at',
        'revoked_at',
        'revoked_by',
        'revoke_reason',
    ];

    protected function casts(): array
    {
        return [
            'used_at'    => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function exam(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function originalSubmission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'original_submission_id');
    }

    public function newSubmission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'new_submission_id');
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────────

    /** Grants that have not been used yet. */
    public function scopeUnused(Builder $query): Builder
    {
        return $query->whereNull('used_at');
    }

    /** Grants that have not been revoked. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at')
                     ->whereNull('used_at')
                     ->where(function (Builder $q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    // ─── Convenience Methods ──────────────────────────────────────────────────

    /**
     * Find the single active grant for a given submission, if any.
     */
    public static function activeForSubmission(int $submissionId): ?self
    {
        return self::active()
            ->where('original_submission_id', $submissionId)
            ->latest()
            ->first();
    }

    /**
     * Find the single active grant for any of a participant's submissions
     * on a specific exam.  Used in ExamTakingController when a participant
     * re-authenticates.
     */
    public static function activeForParticipantOnExam(
        int $examId,
        string $participantType,
        int $participantId
    ): ?self {
        return self::active()
            ->where('general_exam_id', $examId)
            ->whereHas('originalSubmission', function (Builder $q) use ($participantType, $participantId) {
                $q->where('participant_type', $participantType)
                  ->where('participant_id', $participantId);
            })
            ->with('originalSubmission')
            ->latest()
            ->first();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    public function isActive(): bool
    {
        return ! $this->isUsed() && ! $this->isRevoked() && ! $this->isExpired();
    }

    /**
     * Mark this grant as consumed and (for fresh mode) link the new submission.
     */
    public function markUsed(?int $newSubmissionId = null): void
    {
        $this->update([
            'used_at'           => now(),
            'new_submission_id' => $newSubmissionId,
        ]);
    }

    /**
     * Revoke the grant so the candidate can no longer use it.
     */
    public function revoke(int $revokedBy, string $reason = ''): void
    {
        $this->update([
            'revoked_at'    => now(),
            'revoked_by'    => $revokedBy,
            'revoke_reason' => $reason,
        ]);
    }
}