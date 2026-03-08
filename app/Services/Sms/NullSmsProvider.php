<?php

namespace App\Services\Sms;

use App\Channels\Messages\SmsMessage;
use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Log;

/**
 * Null SMS Provider - A placeholder implementation when no SMS provider is configured.
 *
 * This provider logs SMS messages instead of sending them, useful for:
 * - Development environments
 * - Testing without actual SMS costs
 * - When SMS functionality is not yet configured
 */
class NullSmsProvider implements SmsProvider
{
    /**
     * Send an SMS message (logs instead of sending).
     */
    public function send(string $to, SmsMessage $message): bool
    {
        Log::info('NullSmsProvider: SMS would be sent', [
            'to' => $to,
            'content' => $message->getContent(),
            'from' => $message->getFrom(),
        ]);

        return true;
    }

    /**
     * Send an SMS message to multiple phone numbers (logs instead of sending).
     *
     * @return array<string, bool>
     */
    public function sendBulk(array $recipients, SmsMessage $message): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->send($recipient, $message);
        }

        return $results;
    }

    /**
     * Check if the SMS provider is available.
     * Returns false since this is a null implementation.
     */
    public function isAvailable(): bool
    {
        return false;
    }

    /**
     * Get the provider name.
     */
    public function getProviderName(): string
    {
        return 'null';
    }

    /**
     * Get the remaining SMS credits/balance.
     * Returns null as this is not applicable for the null provider.
     */
    public function getBalance(): ?int
    {
        return null;
    }
}
