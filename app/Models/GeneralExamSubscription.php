<?php

namespace App\Models;

use App\Enums\GeneralExamSubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralExamSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'general_exam_subscription_plan_id',
        'type',
        'status',
        'participant_slots',
        'participants_used',
        'exams_used',
        'max_exams',
        'amount_paid',
        'granted_by_owner',
        'granted_by',
        'activated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => GeneralExamSubscriptionStatus::class,
            'amount_paid' => 'decimal:2',
            'granted_by_owner' => 'boolean',
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubscriptionPlan::class, 'general_exam_subscription_plan_id');
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicSubject::class,
            'general_exam_subscription_subjects',
            'general_exam_subscription_id',
            'academic_subject_id'
        )->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(GeneralExamSubscriptionPayment::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(GeneralExam::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query): void
    {
        $query->where('status', GeneralExamSubscriptionStatus::Active)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    // ==================== ACCESSORS / HELPERS ====================

    public function isActive(): bool
    {
        if ($this->status !== GeneralExamSubscriptionStatus::Active) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function hasAvailableParticipantSlots(): bool
    {
        if ($this->type !== 'online') {
            return true;
        }

        return $this->participants_used < $this->participant_slots;
    }

    public function availableParticipantSlots(): int
    {
        return max(0, $this->participant_slots - $this->participants_used);
    }

    public function hasAvailableExamSlots(): bool
    {
        $subjectCount = $this->subjects()->count();
        if ($subjectCount === 0) {
            return false;
        }

        $cyclesPerSubject = max(1, (int) ($this->max_exams ?? 1));
        $allowedTotalGenerations = $subjectCount * $cyclesPerSubject;
        $usedGenerations = $this->exams()->whereNotNull('academic_subject_id')->count();

        return $usedGenerations < $allowedTotalGenerations;
    }

    public function canCreateExam(): bool
    {
        return $this->isActive() && $this->hasAvailableExamSlots();
    }

    public function activate(): void
    {
        $this->update([
            'status' => GeneralExamSubscriptionStatus::Active,
            'activated_at' => now(),
        ]);
    }

    public function incrementExamUsage(): void
    {
        $this->increment('exams_used');
    }

    public function incrementParticipantUsage(int $count = 1): void
    {
        $this->increment('participants_used', $count);
    }
}
