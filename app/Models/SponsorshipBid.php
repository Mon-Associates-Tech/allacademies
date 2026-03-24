<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorshipBid extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    protected $fillable = [
        'sponsorship_offer_id',
        'sponsorship_program_id',
        'user_id',
        'message',
        'status',
        'rejection_reason',
        'responded_at',
    ];
    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Get the sponsorships offer this bid is for
     */
    public function sponsorshipOffer(): BelongsTo
    {
        return $this->belongsTo(SponsorshipOffer::class, 'sponsor_offer_id');
    }

    /**
     * Get the sponsorships project being bid
     */
    public function sponsorshipProject(): BelongsTo
    {
        return $this->belongsTo(SponsorshipProject::class);
    }

    /**
     * Get the user who submitted the bid
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user relationship
     */
    public function bidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the bid is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the bid was accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if the bid was rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Accept the bid
     */
    public function accept(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'responded_at' => now(),
            'rejection_reason' => null,
        ]);

        return true;
    }

    /**
     * Reject the bid
     */
    public function reject(?string $reason = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'responded_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Scope to get pending bids
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get accepted bids
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope to get rejected bids
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
