<?php

namespace App\Contracts;

use App\Channels\Messages\SmsMessage;

/**
 * Interface for SMS service providers.
 *
 * Implement this interface to integrate with different SMS providers
 * such as Twilio, Nexmo, Africa's Talking, Termii, etc.
 */
interface SmsProvider
{
    /**
     * Send an SMS message to a phone number.
     *
     * @param  string  $to  The recipient phone number (E.164 format recommended)
     * @param  SmsMessage  $message  The SMS message to send
     * @return bool Whether the message was sent successfully
     */
    public function send(string $to, SmsMessage $message): bool;

    /**
     * Send an SMS message to multiple phone numbers.
     *
     * @param  array<string>  $recipients  Array of phone numbers
     * @param  SmsMessage  $message  The SMS message to send
     * @return array<string, bool> Array of phone numbers and their send status
     */
    public function sendBulk(array $recipients, SmsMessage $message): array;

    /**
     * Check if the SMS provider is properly configured and available.
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name/identifier.
     */
    public function getProviderName(): string;

    /**
     * Get the remaining SMS credits/balance (if supported by provider).
     *
     * @return int|null Returns null if not supported
     */
    public function getBalance(): ?int;
}
