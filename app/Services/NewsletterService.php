<?php

namespace App\Services;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Mail;

class NewsletterService
{
    public function subscribe(string $email, ?string $name = null, string $source = 'website'): NewsletterSubscription
    {
        $subscription = NewsletterSubscription::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'source' => $source,
            ]
        );

        // Send welcome email
        try {
            Mail::to($email)->send(new NewsletterWelcomeMail($subscription));
        } catch (\Exception $e) {
            \Log::error('Failed to send newsletter welcome email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        return $subscription;
    }

    public function unsubscribe(string $token): bool
    {
        $subscription = NewsletterSubscription::where('subscription_token', $token)->first();

        if ($subscription) {
            $subscription->unsubscribe();

            return true;
        }

        return false;
    }

    public function isSubscribed(string $email): bool
    {
        return NewsletterSubscription::where('email', $email)
            ->where('is_active', true)
            ->exists();
    }

    public function getActiveSubscribersCount(): int
    {
        return NewsletterSubscription::active()->count();
    }
}
