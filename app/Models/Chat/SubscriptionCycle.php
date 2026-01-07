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
        'subscription_group_id',
        'cycle_number',
        'cycle_start_date',
        'cycle_end_date',
        'tokens_allocated',
        'tokens_used',
        'current_price',
        'status',
        'is_topup',
    ];

    protected $casts = [
        'cycle_start_date' => 'datetime',
        'cycle_end_date' => 'datetime',
        'tokens_allocated' => 'integer',
        'tokens_used' => 'integer',
        'current_price' => 'decimal:2',
        'is_topup' => 'boolean',
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
            \Illuminate\Support\Facades\Log::warning('Cannot deduct tokens: insufficient remaining.', [
                'cycle_id' => $this->id,
                'tokens_requested' => $tokens,
                'tokens_remaining' => $this->getTokensRemainingAttribute(),
                'tokens_allocated' => $this->tokens_allocated,
                'tokens_used' => $this->tokens_used,
            ]);

            return false;
        }

        $this->tokens_used += $tokens;
        $result = $this->save();

        if ($result) {
            \Illuminate\Support\Facades\Log::debug('Tokens deducted successfully.', [
                'cycle_id' => $this->id,
                'tokens_deducted' => $tokens,
                'tokens_used_before' => $this->tokens_used - $tokens,
                'tokens_used_after' => $this->tokens_used,
                'tokens_remaining' => $this->getTokensRemainingAttribute(),
            ]);
        } else {
            \Illuminate\Support\Facades\Log::error('Failed to save cycle after token deduction.', [
                'cycle_id' => $this->id,
                'tokens_to_deduct' => $tokens,
            ]);
        }

        return $result;
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

    /**
     * Get remaining days in this cycle
     */
    public function getRemainingDays(): int
    {
        return max(0, now()->diffInDays($this->cycle_end_date, false));
    }

    /**
     * Check if cycle is ending soon (within 3 days)
     */
    public function isEndingSoon(): bool
    {
        return $this->getRemainingDays() <= 3;
    }

    /**
     * Check if cycle is expired or ending
     */
    public function isExpiredOrEnding(): bool
    {
        return $this->isExpired() || $this->isEndingSoon();
    }

    /**
     * Add topup tokens to this cycle
     * If cycle is active, tokens are added; if expired, they go to next cycle
     */
    public function addTopupTokens(int $tokens, bool $carryoverToNextCycle = true): bool
    {
        if ($tokens <= 0) {
            return false;
        }

        $this->tokens_allocated += $tokens;

        return $this->save();
    }

    /**
     * Carryover unused topup tokens to next cycle
     * This is called when current cycle expires and there are unused topup tokens
     *
     * @return int Tokens carried over
     */
    public function carryoverUnusedTopupTokens(): int
    {
        $unusedTokens = $this->getTokensRemainingAttribute();

        if ($unusedTokens <= 0) {
            return 0;
        }

        $nextCycle = $this->user->getNextUpcomingCycle();

        if (! $nextCycle) {
            return 0;
        }

        $nextCycle->addTopupTokens($unusedTokens, false);

        return $unusedTokens;
    }

    /**
     * Check if this cycle can auto-renew
     * Returns true if user has subscription for the next month
     */
    public function canAutoRenew(): bool
    {
        return $this->user->getNextUpcomingCycle() !== null;
    }

    /**
     * Mark cycle as expired and activate next cycle if it exists
     */
    public function expireAndActivateNext(): ?SubscriptionCycle
    {
        $this->update(['status' => 'expired']);

        $nextCycle = $this->user->getNextUpcomingCycle();

        if ($nextCycle) {
            // Carryover any unused topup tokens (only from topups, not from regular allocations)
            $this->carryoverUnusedTopupTokens();

            $nextCycle->update(['status' => 'active']);

            return $nextCycle;
        }

        return null;
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

    /**
     * Scope to get cycles by subscription group ID
     * Used to retrieve all cycles that were created in a single multi-month purchase
     */
    public function scopeByGroup($query, string $groupId)
    {
        return $query->where('subscription_group_id', $groupId);
    }

    /**
     * Get all cycles in this subscription group
     */
    public function getCyclesInGroup()
    {
        if (! $this->subscription_group_id) {
            return collect();
        }

        return static::byGroup($this->subscription_group_id)
            ->orderBy('cycle_number')
            ->get();
    }

    /**
     * Get the total cumulative cost of all cycles in this group
     */
    public function getGroupTotalCost(): string
    {
        if (! $this->subscription_group_id) {
            return $this->current_price;
        }

        $lastCycleInGroup = static::byGroup($this->subscription_group_id)
            ->orderByDesc('cycle_number')
            ->first();

        return $lastCycleInGroup?->current_price ?? $this->current_price;
    }

    /**
     * Get the number of cycles in this subscription group
     */
    public function getGroupCycleCount(): int
    {
        if (! $this->subscription_group_id) {
            return 1;
        }

        return static::byGroup($this->subscription_group_id)->count();
    }

    /**
     * Get the date range for the entire subscription group
     */
    public function getGroupDateRange(): array
    {
        if (! $this->subscription_group_id) {
            return [
                'start' => $this->cycle_start_date,
                'end' => $this->cycle_end_date,
            ];
        }

        $cycles = static::byGroup($this->subscription_group_id)
            ->orderBy('cycle_number')
            ->get();

        return [
            'start' => $cycles->first()?->cycle_start_date,
            'end' => $cycles->last()?->cycle_end_date,
        ];
    }

    /**
     * Scope to get topup cycles only
     */
    public function scopeTopups($query)
    {
        return $query->where('is_topup', true);
    }

    /**
     * Scope to get initial purchase cycles only
     */
    public function scopeInitialPurchases($query)
    {
        return $query->where('is_topup', false);
    }

    /**
     * Check if this is a topup cycle
     */
    public function isTopup(): bool
    {
        return (bool) $this->is_topup;
    }

    /**
     * Get all topup cycles for this user
     */
    public function getAllTopupCycles()
    {
        return static::forUser($this->user_id)
            ->topups()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get topup cycles for a specific pricing tier
     */
    public function getTierupTopups()
    {
        return static::forUser($this->user_id)
            ->topups()
            ->where('pricing_tier_id', $this->pricing_tier_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
