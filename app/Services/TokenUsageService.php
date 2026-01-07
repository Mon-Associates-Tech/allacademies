<?php

namespace App\Services;

use App\Events\TokenUsageUpdated;
use App\Models\Chat\OpenAiTokenUsageLog;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TokenUsageService
{
    /**
     * Log token usage and deduct from user's active subscription cycle.
     * Handles differences between legacy and current OpenAI response formats.
     * Logs to both SubscriptionCycle (primary) and OpenAiTokenUsageLog (fallback) during transition.
     */
    public function logUsage(User $user, array $usage, string $requestType = 'chat', ?string $model = null): void
    {
        $subscriptionCycle = $user->getCurrentActiveCycle();

        if (! $subscriptionCycle) {
            Log::warning('Token usage logging skipped: No active subscription cycle found for user.', [
                'user_id' => $user->id,
                'request_type' => $requestType,
            ]);

            return;
        }

        $promptTokens = $usage['input_tokens'] ?? $usage['prompt_tokens'] ?? 0;
        $completionTokens = $usage['output_tokens'] ?? $usage['completion_tokens'] ?? 0;
        $totalTokens = $usage['total_tokens'] ?? ($promptTokens + $completionTokens);

        try {
            // Primary: Check if user has sufficient tokens
            if (! $subscriptionCycle->hasTokens($totalTokens)) {
                Log::warning('Insufficient tokens in active subscription cycle.', [
                    'user_id' => $user->id,
                    'required_tokens' => $totalTokens,
                    'available_tokens' => $subscriptionCycle->getTokensRemainingAttribute(),
                    'subscription_cycle_id' => $subscriptionCycle->id,
                    'cycle_status' => $subscriptionCycle->status,
                    'cycle_active' => $subscriptionCycle->isActive(),
                ]);

                return;
            }

            // Deduct tokens from subscription cycle
            $deductionSuccess = $subscriptionCycle->deductTokens($totalTokens);

            if (! $deductionSuccess) {
                Log::error('Failed to deduct tokens from subscription cycle.', [
                    'user_id' => $user->id,
                    'subscription_cycle_id' => $subscriptionCycle->id,
                    'tokens_to_deduct' => $totalTokens,
                    'tokens_remaining' => $subscriptionCycle->getTokensRemainingAttribute(),
                ]);

                return;
            }

            // Reload cycle to get updated values
            $subscriptionCycle->refresh();

            // Create usage log record for historical tracking
            OpenAiTokenUsageLog::create([
                'user_id' => $user->id,
                'subscription_id' => null,
                'subscription_cycle_id' => $subscriptionCycle->id,
                'model' => $model,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'request_type' => $requestType,
                'context' => null,
            ]);

            // Dispatch event for observers/listeners
            event(new TokenUsageUpdated($user, $subscriptionCycle, $totalTokens));

            Log::info('Token usage logged successfully.', [
                'user_id' => $user->id,
                'subscription_cycle_id' => $subscriptionCycle->id,
                'tokens_deducted' => $totalTokens,
                'tokens_used_total' => $subscriptionCycle->tokens_used,
                'tokens_remaining' => $subscriptionCycle->getTokensRemainingAttribute(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging token usage.', [
                'user_id' => $user->id,
                'subscription_cycle_id' => $subscriptionCycle->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Deduct tokens from user's current cycle
     */
    public function deductTokens(User $user, int $tokens): bool
    {
        $cycle = $user->getCurrentActiveCycle();

        if (! $cycle) {
            Log::warning('No active subscription cycle found for token deduction', [
                'user_id' => $user->id,
                'tokens_requested' => $tokens,
            ]);

            return false;
        }

        $success = $cycle->deductTokens($tokens);

        if ($success) {
            Log::info('Tokens deducted from cycle', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'tokens_deducted' => $tokens,
                'tokens_remaining' => $cycle->getTokensRemainingAttribute(),
            ]);
        } else {
            Log::warning('Insufficient tokens for deduction', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'tokens_available' => $cycle->getTokensRemainingAttribute(),
                'tokens_requested' => $tokens,
            ]);
        }

        return $success;
    }

    /**
     * Check if user has sufficient tokens
     */
    public function hasAvailableTokens(User $user, int $requiredTokens = 1): bool
    {
        $cycle = $user->getCurrentActiveCycle();

        return $cycle ? $cycle->hasTokens($requiredTokens) : false;
    }

    /**
     * Get usage stats for a cycle
     */
    public function getCycleUsageStats(SubscriptionCycle $cycle): array
    {
        $usageLogs = $cycle->usageLogs()->get();

        return [
            'cycle_id' => $cycle->id,
            'cycle_number' => $cycle->cycle_number,
            'total_allocated' => $cycle->tokens_allocated,
            'total_used' => $cycle->tokens_used,
            'total_remaining' => $cycle->getTokensRemainingAttribute(),
            'usage_percentage' => $cycle->usage_percentage,
            'remaining_percentage' => 100 - $cycle->usage_percentage,
            'usage_count' => $usageLogs->count(),
            'average_tokens_per_usage' => $usageLogs->count() > 0 ? round($cycle->tokens_used / $usageLogs->count(), 2) : 0,
            'cycle_start_date' => $cycle->cycle_start_date,
            'cycle_end_date' => $cycle->cycle_end_date,
            'days_remaining' => $cycle->getRemainingDays(),
            'is_nearing_depletion' => $cycle->isNearingDepletion(),
        ];
    }

    /**
     * Get usage stats for a user's current cycle
     */
    public function getUserCurrentCycleStats(User $user): ?array
    {
        $cycle = $user->getCurrentActiveCycle();

        return $cycle ? $this->getCycleUsageStats($cycle) : null;
    }

    /**
     * Get usage history for a user (with pagination)
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserUsageHistory(User $user, int $perPage = 50)
    {
        return OpenAiTokenUsageLog::where('user_id', $user->id)
            ->with('subscriptionCycle')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get usage history for a specific cycle
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCycleUsageHistory(SubscriptionCycle $cycle, int $perPage = 50)
    {
        return $cycle->usageLogs()
            ->latest()
            ->paginate($perPage);
    }
}
