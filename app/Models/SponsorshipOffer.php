<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SponsorshipOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sponsorship_offers';

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_FULFILLED = 'fulfilled';
    protected $fillable = [
        'user_id',
        'title',
        'code',
        'description',
        'amount_offered',
        'criteria',
        'status',
        'accepts_bids',
        'expires_at',
        'metadata',
    ];
    protected $casts = [
        'amount_offered' => 'decimal:2',
        'accepts_bids' => 'boolean',
        'expires_at' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_FULFILLED => 'Fulfilled',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($offer) {
            if (empty($offer->code)) {
                $offer->code = strtoupper('SPO-' . Str::random(8));
            }
        });
    }

    /**
     * Get the sponsor who created this offer
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
     * Get pending bids
     */
    public function pendingBids(): HasMany
    {
        return $this->bids()->where('status', SponsorshipBid::STATUS_PENDING);
    }

    /**
     * Get the bids submitted for this offer
     */
    public function bids(): HasMany
    {
        return $this->hasMany(SponsorshipBid::class);
    }

    /**
     * Get accepted bids
     */
    public function acceptedBids(): HasMany
    {
        return $this->bids()->where('status', SponsorshipBid::STATUS_ACCEPTED);
    }

    /**
     * Check if the offer accepts bids
     */
    public function canAcceptBids(): bool
    {
        return $this->accepts_bids && $this->isOpen();
    }

    /**
     * Check if the offer is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN && !$this->isExpired();
    }

    /**
     * Check if the offer has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Close the offer
     */
    public function close(): bool
    {
        if ($this->status === self::STATUS_CLOSED) {
            return false;
        }

        $this->update(['status' => self::STATUS_CLOSED]);
        return true;
    }

    /**
     * Mark the offer as fulfilled
     */
    public function markAsFulfilled(): bool
    {
        $this->update(['status' => self::STATUS_FULFILLED]);
        return true;
    }

    /**
     * Reopen the offer
     */
    public function reopen(): bool
    {
        if ($this->status !== self::STATUS_CLOSED) {
            return false;
        }

        $this->update(['status' => self::STATUS_OPEN]);
        return true;
    }

    /**
     * Get total amount contributed through this offer
     */
    public function getTotalContributedAttribute(): float
    {
        return $this->contributions()
            ->where('status', SponsorshipContribution::STATUS_COMPLETED)
            ->sum('amount');
    }

    /**
     * Get the contributions made through this offer
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(SponsorshipContribution::class);
    }

    /**
     * Get remaining amount to offer
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount_offered - $this->total_contributed);
    }

    /**
     * Scope to get only open offers
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to get offers accepting bids
     */
    public function scopeAcceptingBids($query)
    {
        return $query->open()->where('accepts_bids', true);
    }
}
