<?php

namespace App\Traits;

use App\Models\Chat\SubscriptionCycle;

trait ChecksTokenAvailability
{
    /**
     * Check if user has available tokens
     *
     * @param  int  $requiredTokens  Minimum tokens required
     * @return array ['available' => bool, 'message' => string|null, 'cycle' => SubscriptionCycle|null]
     */
    public function checkTokenAvailability(int $requiredTokens = 200): array
    {
        $user = auth()->user();

        if (! $user) {
            return [
                'available' => false,
                'message' => 'no_user',
                'cycle' => null,
            ];
        }

        $cycle = $user->subscriptionCycles()
            ->where('status', 'active')
            ->first();

        if (! $cycle) {
            return [
                'available' => false,
                'message' => 'no_subscription',
                'cycle' => null,
            ];
        }

        if ($cycle->isExpired()) {
            $cycle->update(['status' => 'expired']);

            return [
                'available' => false,
                'message' => 'expired',
                'cycle' => $cycle,
            ];
        }

        if ($cycle->getTokensRemainingAttribute() <= 0) {
            return [
                'available' => false,
                'message' => 'depleted',
                'cycle' => $cycle,
            ];
        }

        if ($cycle->getTokensRemainingAttribute() < $requiredTokens) {
            return [
                'available' => false,
                'message' => 'insufficient',
                'cycle' => $cycle,
            ];
        }

        return [
            'available' => true,
            'message' => null,
            'cycle' => $cycle,
        ];
    }

    /**
     * Check if user can send message (alias for checkTokenAvailability)
     */
    public function canSendMessage(int $requiredTokens = 200): bool
    {
        return $this->checkTokenAvailability($requiredTokens)['available'];
    }

    /**
     * Get token warning message
     */
    public function getTokenWarningMessage(int $requiredTokens = 200): ?string
    {
        return $this->checkTokenAvailability($requiredTokens)['message'];
    }
}
