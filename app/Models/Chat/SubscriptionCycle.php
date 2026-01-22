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
        'merged_with_group_id',
        'cycle_number',
        'cycle_start_date',
        'cycle_end_date',
        'tokens_allocated',
        'topup_tokens_allocated',
        'tokens_used',
        'current_price',
        'status',
        'is_topup',
        'is_trial',
        'is_merged',
        'allocated_by_admin',
    ];

    protected $casts = [
        'cycle_start_date' => 'datetime',
        'cycle_end_date' => 'datetime',
        'tokens_allocated' => 'integer',
        'topup_tokens_allocated' => 'integer',
        'tokens_used' => 'integer',
        'current_price' => 'decimal:2',
        'is_topup' => 'boolean',
        'is_trial' => 'boolean',
        'is_merged' => 'boolean',
        'allocated_by_admin' => 'boolean',
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
     * Deduct tokens from cycle
     * Smart deduction: uses base allocation first, then topup if needed
     */
    public function deductTokens(int $tokens): bool
    {
        if ($this->getTokensRemainingAttribute() < $tokens) {
            \Illuminate\Support\Facades\Log::warning('Cannot deduct tokens: insufficient remaining.', [
                'cycle_id' => $this->id,
                'tokens_requested' => $tokens,
                'tokens_remaining' => $this->getTokensRemainingAttribute(),
                'base_remaining' => $this->getBaseTokensRemaining(),
                'topup_remaining' => $this->getTopupTokensRemaining(),
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
                'tokens_used_after' => $this->tokens_used,
                'base_remaining' => $this->getBaseTokensRemaining(),
                'topup_remaining' => $this->getTopupTokensRemaining(),
                'total_remaining' => $this->getTokensRemainingAttribute(),
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
     * Get the remaining tokens for this cycle
     */
    public function getTokensRemainingAttribute(): int
    {
        return $this->tokens_allocated - $this->tokens_used;
    }

    /**
     * Get remaining base tokens (without topups)
     */
    public function getBaseTokensRemaining(): int
    {
        return $this->getBaseTokensAllocated() - $this->tokens_used;
    }

    /**
     * Get base tokens allocated (without topups)
     */
    public function getBaseTokensAllocated(): int
    {
        return $this->tokens_allocated - $this->topup_tokens_allocated;
    }

    /**
     * Get remaining topup tokens
     */
    public function getTopupTokensRemaining(): int
    {
        return $this->topup_tokens_allocated - max(0, $this->tokens_used - $this->getBaseTokensAllocated());
    }

    /**
     * Check if cycle has enough tokens
     */
    public function hasTokens(int $requiredTokens = 1): bool
    {
        return $this->isActive() && $this->getTokensRemainingAttribute() >= $requiredTokens;
    }

    /**
     * Check if cycle is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && ! $this->isExpired();
    }

    /**
     * Check if nearing depletion (above 90% usage)
     */
    public function isNearingDepletion(): bool
    {
        return $this->usage_percentage >= 90;
    }

    /**
     * Check if cycle is expired or ending
     */
    public function isExpiredOrEnding(): bool
    {
        return $this->isExpired() || $this->isEndingSoon();
    }

    /**
     * Check if cycle has expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->cycle_end_date);
    }

    /**
     * Check if cycle is ending soon (within 3 days)
     */
    public function isEndingSoon(): bool
    {
        return $this->getRemainingDays() <= 3;
    }

    /**
     * Get remaining days in this cycle
     */
    public function getRemainingDays(): int
    {
        return max(0, now()->diffInDays($this->cycle_end_date, false));
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

    /**
     * Carryover unused topup tokens to next cycle
     * This is called when current cycle expires and there are unused topup tokens
     * Base allocation is NOT carried over - only topup tokens
     * After carryover, topup tokens are cleared from current cycle
     *
     * @return int Tokens carried over
     */
    public function carryoverUnusedTopupTokens(): int
    {
        $unusedTopup = $this->calculateUnusedTopupTokens();

        if ($unusedTopup <= 0) {
            \Illuminate\Support\Facades\Log::info('No topup tokens to carry over', [
                'cycle_id' => $this->id,
                'topup_allocated' => $this->topup_tokens_allocated,
                'tokens_used' => $this->tokens_used,
            ]);

            return 0;
        }

        $nextCycle = $this->user->getNextUpcomingCycle();

        if (! $nextCycle) {
            \Illuminate\Support\Facades\Log::info('No next cycle to carry over topup tokens', [
                'cycle_id' => $this->id,
                'topup_tokens_unused' => $unusedTopup,
            ]);

            return 0;
        }

        $nextCycle->addTopupTokens($unusedTopup);

        // Clear topup tokens from current cycle after carryover
        // $this->tokens_allocated -= $this->topup_tokens_allocated;
        $this->topup_tokens_allocated = 0;
        $this->save();

        \Illuminate\Support\Facades\Log::info('Topup tokens carried over to next cycle', [
            'current_cycle_id' => $this->id,
            'next_cycle_id' => $nextCycle->id,
            'topup_tokens_carried' => $unusedTopup,
        ]);

        return $unusedTopup;
    }

    /**
     * Calculate unused topup tokens only (not base allocation)
     * Base allocation is NEVER carried over, only unused topups
     */
    public function calculateUnusedTopupTokens(): int
    {
        $baseUsed = min($this->tokens_used, $this->getBaseTokensAllocated());
        $topupUsed = $this->tokens_used - $baseUsed;
        $unusedTopup = max(0, $this->topup_tokens_allocated - $topupUsed);

        return $unusedTopup;
    }

    /**
     * Add topup tokens to this cycle
     * Topups are added as separate tokens that can be carried over
     */
    public function addTopupTokens(int $tokens): bool
    {
        if ($tokens <= 0) {
            return false;
        }

        // Add to topup allocation and increase total allocation
        $this->topup_tokens_allocated += $tokens;
        $this->tokens_allocated += $tokens;

        $result = $this->save();

        if ($result) {
            \Illuminate\Support\Facades\Log::info('Topup tokens added to cycle', [
                'cycle_id' => $this->id,
                'tokens_added' => $tokens,
                'topup_tokens_allocated' => $this->topup_tokens_allocated,
                'total_tokens_allocated' => $this->tokens_allocated,
            ]);
        }

        return $result;
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
     * Scope to get trial cycles only
     */
    public function scopeTrials($query)
    {
        return $query->where('is_trial', true);
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
     * Check if this is a trial cycle
     */
    public function isTrial(): bool
    {
        return (bool) $this->is_trial;
    }

    /**
     * Check if this is a topup cycle
     */
    public function isTopup(): bool
    {
        return (bool) $this->is_topup;
    }

    /**
     * Check if this cycle is merged with another subscription
     */
    public function isMerged(): bool
    {
        return (bool) $this->is_merged;
    }

    /**
     * Get the merged subscription group if exists
     */
    public function getMergedCycles()
    {
        if (! $this->merged_with_group_id) {
            return collect();
        }

        return static::byGroup($this->merged_with_group_id)->get();
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
     * Scope to get admin-allocated cycles only
     */
    public function scopeAllocatedByAdmin($query)
    {
        return $query->where('allocated_by_admin', true);
    }

    /**
     * Check if this cycle was allocated by admin
     */
    public function isAllocatedByAdmin(): bool
    {
        return (bool) $this->allocated_by_admin;
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
