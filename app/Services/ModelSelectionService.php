<?php

namespace App\Services;

use App\Models\User;

class ModelSelectionService
{
    /**
     * Get the appropriate OpenAI model based on user's subscription
     */
    public function getModelForUser(User $user): string
    {
        // Non-subscribers always get premium model
        if ($user->role !== \App\Enums\UserRole::SUBSCRIBER) {
            return config('openai.openai.premium_model', 'gpt-4-turbo');
        }

        // For subscribers, check their active subscription
        $subscription = $user->activeTokenSubscription;

        if (!$subscription || !$subscription->package) {
            return config('openai.openai.default_model', 'gpt-4.1-nano');
        }

        // If package has a specific model defined, use it
        if (!empty($subscription->package->model)) {
            return $subscription->package->model;
        }

        // Determine model based on package type
        if ($subscription->package->is_free || $subscription->package->price == 0) {
            return config('openai.openai.default_model', 'gpt-4.1-nano');
        }

        // Paid packages get premium model
        return config('openai.openai.premium_model', 'gpt-4-turbo');
    }

    /**
     * Check if user has access to premium models
     */
    public function hasPremiumAccess(User $user): bool
    {
        $model = $this->getModelForUser($user);
        $premiumModel = config('openai.openai.premium_model', 'gpt-4-turbo');

        return $model === $premiumModel;
    }

    /**
     * Get appropriate model for image generation
     */
    public function getImageModelForUser(User $user): string
    {
        // Image generation typically requires premium access
        if ($this->hasPremiumAccess($user)) {
            return 'gpt-image-1';
        }

        return 'gpt-image-1'; // Fallback to standard image model
    }

    /**
     * Get appropriate model for text generation
     */
    public function getTextModelForUser(User $user): string
    {
        return $this->getModelForUser($user);
    }
}
