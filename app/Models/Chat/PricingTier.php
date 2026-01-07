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
     */
    public function isInInitialPeriod(\DateTime $subscriptionStartDate): bool
    {
        $monthsElapsed = now()->diffInMonths($subscriptionStartDate);

        return $monthsElapsed < $this->initial_period_months;
    }

    /**
     * Get the monthly price increment based on the cycle number
     * Months 1-6 use initial_price, Month 7+ uses subsequent_price
     *
     * @param  int  $cycleNumber  The cycle number (1-based)
     * @return float The monthly price increment
     */
    public function getMonthlyPriceIncrement(int $cycleNumber): float
    {
        return $cycleNumber <= $this->initial_period_months
            ? (float) $this->initial_price
            : (float) $this->subsequent_price;
    }

    /**
     * Get the cumulative total price up to and including a specific cycle
     * Example: Basic plan with initial=$10, subsequent=$5
     * Cycle 1: $10
     * Cycle 2: $20
     * Cycle 6: $60
     * Cycle 7: $65 (60 + 5)
     * Cycle 8: $70 (65 + 5)
     *
     * @param  int  $cycleNumber  The cycle number (1-based)
     * @return float The total cumulative price
     */
    public function getCumulativePriceUpToCycle(int $cycleNumber): float
    {
        $totalPrice = 0;

        for ($i = 1; $i <= $cycleNumber; $i++) {
            $totalPrice += $this->getMonthlyPriceIncrement($i);
        }

        return $totalPrice;
    }

    /**
     * Get the price for a specific cycle (price delta, not cumulative)
     *
     * @param  int  $cycleNumber  The cycle number (1-based)
     * @return float The price for this cycle only
     */
    public function getPriceForCycle(int $cycleNumber): float
    {
        return $this->getMonthlyPriceIncrement($cycleNumber);
    }

    /**
     * Get the current price based on subscription start date (legacy method)
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
