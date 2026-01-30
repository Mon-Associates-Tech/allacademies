<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class ModelSelectionService
{
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
     * Check if user has access to premium models
     */
    public function hasPremiumAccess(User $user): bool
    {
        $model = $this->getModelForUser($user);
        $premiumModel = config('openai.openai.premium_model', 'gpt-4-turbo');

        return $model === $premiumModel;
    }

    /**
     * Get the appropriate OpenAI model based on user's subscription
     */
    public function getModelForUser(User $user): string
    {
        // Non-guests always get premium model
        if ($user->role !== \App\Enums\UserRole::GUEST) {
            return config('openai.openai.premium_model', 'gpt-4-turbo');
        }

        // For guests, check their active subscription cycle
        $cycle = $user->getCurrentActiveCycle();

        if (! $cycle || ! $cycle->pricingTier) {
            return config('openai.openai.default_model', 'gpt-4.1-nano');
        }

        // Determine model based on pricing tier
        if ($cycle->pricingTier->name === 'Premium') {
            return config('openai.openai.premium_model', 'gpt-4-turbo');
        }

        // Basic tier gets default model
        return config('openai.openai.default_model', 'gpt-4.1-nano');
    }

    /**
     * Get appropriate model for text generation
     */
    public function getTextModelForUser(User $user): string
    {
        return $this->getModelForUser($user);
    }

    /**
     * Detect if the request is for image or text using a small LLM call.
     */
    public function detectModelType(ChatGPTService $chatGPT, array $parameters, array $conversationHistory): string
    {
        $user = auth()->user();
        $classificationModel = config('openai.openai.default_model', 'gpt-4.1-nano');

        $prompt = [
            [
                'role' => 'system',
                'content' => 'Analyze the user request and respond with exactly one word: "image" if the request involves generating, creating, drawing, or visualizing something graphical/diagrammatic, or "text" for all other requests.',
            ],
            [
                'role' => 'user',
                'content' => 'User request: '.($parameters['input'] ?? '').
                    "\n\nContext: ".json_encode(array_slice($conversationHistory, -3)),
            ],
        ];

        try {
            // We use the basic model for classification to save costs
            $result = $chatGPT->chat($prompt, $classificationModel, []);

            $type = trim(strtolower($result['content'] ?? 'text'));

            Log::info('Model detection result', ['model' => $classificationModel, 'type' => $type]);

            return $type === 'image' ? 'image' : 'text';
        } catch (\Exception $e) {
            Log::error('Model detection failed', ['error' => $e->getMessage()]);

            return 'text';
        }
    }
}
