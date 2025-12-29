<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingTier extends Model
{
    use HasFactory;

    protected $table = 'pricing_tiers';

    protected $fillable = [
        'name',
        'model',
        'initial_price',
        'subsequent_price',
        'monthly_token_limit',
        'initial_period_months',
        'description',
        'is_active',
    ];

    protected $casts = [
        'initial_price' => 'decimal:2',
        'subsequent_price' => 'decimal:2',
        'monthly_token_limit' => 'integer',
        'initial_period_months' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get subscription cycles associated with this pricing tier
     */
    public function subscriptionCycles(): HasMany
    {
        return $this->hasMany(SubscriptionCycle::class, 'pricing_tier_id');
    }

    /**
     * Determine if user is in the initial pricing period
     * @param \DateTime $subscriptionStartDate
     * @return bool
     */
    public function isInInitialPeriod(\DateTime $subscriptionStartDate): bool
    {
        $monthsElapsed = now()->diffInMonths($subscriptionStartDate);
        return $monthsElapsed < $this->initial_period_months;
    }

    /**
     * Get the current price based on subscription start date
     * @param \DateTime $subscriptionStartDate
     * @return float
     */
    public function getCurrentPrice(\DateTime $subscriptionStartDate): float
    {
        return $this->isInInitialPeriod($subscriptionStartDate)
            ? (float) $this->initial_price
            : (float) $this->subsequent_price;
    }

    /**
     * Check if this is the basic tier
     */
    public function isBasic(): bool
    {
        return strtolower($this->name) === 'basic';
    }

    /**
     * Check if this is the premium tier
     */
    public function isPremium(): bool
    {
        return strtolower($this->name) === 'premium';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
