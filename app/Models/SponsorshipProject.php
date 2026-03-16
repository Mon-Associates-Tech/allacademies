<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SponsorshipProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sponsorship_programs';
    protected $fillable = [
        'user_id',
        'school_id',
        'type',
        'name',
        'code',
        'description',
        'affected_individuals',
        'amount_goal',
        'amount_raised',
        'deadline',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'rejected_at',
        'metadata',
        'images',
        'videos',
    ];

    protected $casts = [
        'amount_goal' => 'decimal:2',
        'amount_raised' => 'decimal:2',
        'deadline' => 'date',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'metadata' => 'array',
        'images' => 'array',
        'videos' => 'array',
    ];

    public const TYPE_PROJECT = 'project';

    public const TYPE_CAUSE = 'cause';

    public const TYPE_SCHOLARSHIP = 'scholarship';

    public const TYPE_EMERGENCY = 'emergency';

    const STATUS_DRAFT = 'draft';

    const STATUS_PENDING_VERIFICATION = 'pending_verification';

    const STATUS_ACTIVE = 'active';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->code)) {
                $project->code = strtoupper('SPP-'.Str::random(8));
            }
        });
    }

    /**
     * Get the creator/benefactor of the project
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user relationship
     */
    public function benefactor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the school if project is tied to one
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who verified the project
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the beneficiaries of this project
     */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(SponsorshipBeneficiary::class);
    }

    /**
     * Get the bids submitted for this project
     */
    public function bids(): HasMany
    {
        return $this->hasMany(SponsorshipBid::class);
    }

    /**
     * Get the contributions made to this project
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(SponsorshipContribution::class);
    }

    /**
     * Calculate the amount left to reach the goal
     */
    public function getAmountLeftAttribute(): float
    {
        return max(0, $this->amount_goal - $this->amount_raised);
    }

    /**
     * Calculate the progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->amount_goal <= 0) {
            return 0;
        }

        return min(100, round(($this->amount_raised / $this->amount_goal) * 100, 1));
    }

    /**
     * Check if the project is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the project is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->status === self::STATUS_PENDING_VERIFICATION;
    }

    /**
     * Check if the goal has been reached
     */
    public function isGoalReached(): bool
    {
        return $this->amount_raised >= $this->amount_goal;
    }

    /**
     * Check if the deadline has passed
     */
    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    /**
     * Submit the project for verification
     */
    public function submitForVerification(): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        $this->update(['status' => self::STATUS_PENDING_VERIFICATION]);

        return true;
    }

    /**
     * Verify/approve the project
     */
    public function verify(User $verifier): bool
    {
        if ($this->status !== self::STATUS_PENDING_VERIFICATION) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return true;
    }

    /**
     * Reject the project
     */
    public function reject(User $verifier, string $reason): bool
    {
        if ($this->status !== self::STATUS_PENDING_VERIFICATION) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_DRAFT,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        return true;
    }

    /**
     * Scope to get only active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get projects pending verification
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status', self::STATUS_PENDING_VERIFICATION);
    }

    /**
     * Scope to get projects by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get available types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PROJECT => 'Project',
            self::TYPE_CAUSE => 'Cause',
            self::TYPE_SCHOLARSHIP => 'Scholarship',
            self::TYPE_EMERGENCY => 'Emergency',
        ];
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_VERIFICATION => 'Pending Verification',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
