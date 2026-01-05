<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionCycle extends Model
{
    use HasFactory;

    protected $table = 'subscription_cycles';

    protected $fillable = [
        'user_id',
        'pricing_tier_id',
        'cycle_number',
        'cycle_start_date',
        'cycle_end_date',
        'tokens_allocated',
        'tokens_used',
        'current_price',
        'status',
    ];

    protected $casts = [
        'cycle_start_date' => 'datetime',
        'cycle_end_date' => 'datetime',
        'tokens_allocated' => 'integer',
        'tokens_used' => 'integer',
        'current_price' => 'decimal:2',
    ];

    /**
     * Get the user associated with this subscription cycle
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pricing tier associated with this subscription cycle
     */
    public function pricingTier(): BelongsTo
    {
        return $this->belongsTo(PricingTier::class, 'pricing_tier_id');
    }

    public function usageLogs()
    {
        return $this->hasMany(OpenAiTokenUsageLog::class, 'subscription_cycle_id');
    }

    /**
     * Get the remaining tokens for this cycle
     */
    public function getTokensRemainingAttribute(): int
    {
        return $this->tokens_allocated - $this->tokens_used;
    }

    /**
     * Get the usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->tokens_allocated === 0) {
            return 0;
        }
        return round(($this->tokens_used / $this->tokens_allocated) * 100, 2);
    }

    /**
     * Check if cycle is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
            now()->between($this->cycle_start_date, $this->cycle_end_date);
    }

    /**
     * Check if cycle has expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->cycle_end_date);
    }

    /**
     * Deduct tokens from cycle
     */
    public function deductTokens(int $tokens): bool
    {
        if ($this->getTokensRemainingAttribute() < $tokens) {
            return false;
        }

        $this->tokens_used += $tokens;
        return $this->save();
    }

    /**
     * Check if cycle has enough tokens
     */
    public function hasTokens(int $requiredTokens = 1): bool
    {
        return $this->isActive() && $this->getTokensRemainingAttribute() >= $requiredTokens;
    }

    /**
     * Check if nearing depletion (above 90% usage)
     */
    public function isNearingDepletion(): bool
    {
        return $this->usage_percentage >= 90;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
