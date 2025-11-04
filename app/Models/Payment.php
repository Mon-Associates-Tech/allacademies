<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'currency',
        'reference',
        'status',
        'subscription_id',
        'book_subscription_id',
        'token_subscription_id',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'notes' => 'array',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }


    public function bookSubscription()
    {
        return $this->belongsTo(BookSubscription::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get revenue split information
     */
    public function getRevenueSplitAttribute(): ?array
    {
        if (is_array($this->notes) && isset($this->notes['revenue_split'])) {
            return $this->notes['revenue_split'];
        }
        return null;
    }

    /**
     * Check if this payment has revenue split
     */
    public function hasRevenueSplit(): bool
    {
        return $this->revenue_split !== null;
    }

    /**
     * Get platform amount from split
     */
    public function getPlatformAmountAttribute(): float
    {
        return $this->revenue_split['platform_amount'] ?? 0;
    }

    /**
     * Get author amount from split
     */
    public function getAuthorAmountAttribute(): float
    {
        return $this->revenue_split['author_amount'] ?? 0;
    }
}
