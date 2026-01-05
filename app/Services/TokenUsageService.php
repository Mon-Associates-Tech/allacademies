<?php

namespace App\Services;

use App\Events\TokenUsageUpdated;
use App\Models\Chat\OpenAiTokenUsageLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TokenUsageService
{
    /**
     * Log token usage and deduct from user's active subscription.
     * Handles differences between legacy and current OpenAI response formats.
     */
public function logUsage(User $user, array $usage, string $requestType = 'chat', ?string $model = null): void
{
    $subscription = $user->activeTokenSubscription;
    $subscriptionCycle = $user->subscriptionCycles()->where('status', 'active')->latest()->first();

    if (!$subscription && !$subscriptionCycle) {
        Log::warning('Token usage logging skipped: No active subscription found for user.', [
            'user_id' => $user->id,
            'request_type' => $requestType
        ]);
        return;
    }

    // Prefer subscription cycle if available, otherwise use legacy subscription
    $targetId = $subscriptionCycle ? $subscriptionCycle->id : $subscription->id;
    $targetType = $subscriptionCycle ? 'subscription_cycle' : 'user_token_subscription';

    $promptTokens = $usage['input_tokens'] ?? $usage['prompt_tokens'] ?? 0;
    $completionTokens = $usage['output_tokens'] ?? $usage['completion_tokens'] ?? 0;
    $totalTokens = $usage['total_tokens'] ?? ($promptTokens + $completionTokens);

    try {
        // Create usage log
        $usageLog = OpenAiTokenUsageLog::create([
            'user_id' => $user->id,
            'subscription_id' => $targetId,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'request_type' => $requestType,
        ]);

        // Deduct tokens from appropriate subscription
        if ($subscriptionCycle) {
            $subscriptionCycle->deductTokens($totalTokens);
        } else {
            $subscription->deductTokens($totalTokens);
        }

        event(new TokenUsageUpdated($user->id));

    } catch (\Exception $e) {
        Log::error('Failed to log token usage or deduct tokens', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'usage_data' => $usage
        ]);
    }
}
}
