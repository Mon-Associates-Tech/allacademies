<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorshipContribution extends Model
{
    use HasFactory;

    const STATUS_PLEDGED = 'pledged';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const PLATFORM_FEE_PERCENTAGE = 0.01;
    protected $fillable = [
        'sponsorship_project_id',
        'sponsorship_offer_id',
        'user_id',
        'payer_name',
        'payer_email',
        'payer_phone',
        'amount',
        'platform_fee',
        'sponsor_covers_fee',
        'total_charged',
        'net_amount',
        'currency',
        'status',
        'payment_reference',
        'transaction_id',
        'authorization_url',
        'paystack_response',
        'metadata',
        'paid_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_charged' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'sponsor_covers_fee' => 'boolean',
        'paystack_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ]; // 1%

    /**
     * Calculate net amount (what benefactor receives)
     */
    public static function calculateNetAmount(float $amount, bool $sponsorCoversFee = false): float
    {
        if ($sponsorCoversFee) {
            return $amount;
        }

        return $amount - self::calculatePlatformFee($amount);
    }

    /**
     * Calculate platform fee for a given amount
     */
    public static function calculatePlatformFee(float $amount): float
    {
        return round($amount * self::PLATFORM_FEE_PERCENTAGE, 2);
    }

    /**
     * Calculate total to charge sponsor
     */
    public static function calculateTotalCharged(float $amount, bool $sponsorCoversFee = false): float
    {
        if ($sponsorCoversFee) {
            return $amount + self::calculatePlatformFee($amount);
        }

        return $amount;
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PLEDGED => 'Pledged',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    /**
     * Get the platform fee percentage as a display string
     */
    public static function getPlatformFeePercentageDisplay(): string
    {
        return (self::PLATFORM_FEE_PERCENTAGE * 100) . '%';
    }

    /**
     * Get the sponsorships program this contribution is for
     */
    public function sponsorshipProject(): BelongsTo
    {
        return $this->belongsTo(SponsorshipProject::class);
    }

    /**
     * Get the sponsor offer this contribution is through
     */
    public function sponsorshipOffer(): BelongsTo
    {
        return $this->belongsTo(SponsorshipOffer::class);
    }

    /**
     * Get the user who made the contribution
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user relationship
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the contribution is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the contribution is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the contribution failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark the contribution as completed
     */
    public function markAsCompleted(array $paystackResponse = []): bool
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
            'paystack_response' => array_merge(
                $this->paystack_response ?? [],
                $paystackResponse
            ),
        ]);

        // Update the program's amount_raised
        if ($this->sponsorship_program_id) {
            $this->sponsorshipProgram->increment('amount_raised', $this->net_amount);
        }

        return true;
    }

    /**
     * Mark the contribution as failed
     */
    public function markAsFailed(array $paystackResponse = []): bool
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'paystack_response' => array_merge(
                $this->paystack_response ?? [],
                $paystackResponse
            ),
        ]);

        return true;
    }

    /**
     * Mark the contribution as refunded
     */
    public function markAsRefunded(): bool
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            return false;
        }

        $this->update(['status' => self::STATUS_REFUNDED]);

        // Decrease the program's amount_raised
        if ($this->sponsorship_program_id) {
            $this->sponsorshipProgram->decrement('amount_raised', $this->net_amount);
        }

        return true;
    }

    /**
     * Scope to get completed contributions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get pending contributions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
