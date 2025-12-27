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

        if (!$subscription) {
            Log::warning('Token usage logging skipped: No active subscription found for user.', [
                'user_id' => $user->id,
                'request_type' => $requestType
            ]);
            return;
        }

        // Handle key mismatches between completion API and response API
        $promptTokens = $usage['input_tokens'] ?? $usage['prompt_tokens'] ?? 0;
        $completionTokens = $usage['output_tokens'] ?? $usage['completion_tokens'] ?? 0;

        // Calculate total if not explicitly provided or to ensure accuracy
        $totalTokens = $usage['total_tokens'] ?? ($promptTokens + $completionTokens);

        try {
            // Create usage log
            OpenAiTokenUsageLog::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'model' => $model,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'request_type' => $requestType,
            ]);

            // Deduct tokens from subscription
            $subscription->deductTokens($totalTokens);

            // Dispatch event for real-time updates
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
