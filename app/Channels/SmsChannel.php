<?php

namespace App\Channels;

use App\Channels\Messages\SmsMessage;
use App\Contracts\SmsProvider;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function __construct(
        protected SmsProvider $provider
    ) {}

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! $this->provider->isAvailable()) {
            Log::warning('SMS channel is not available. SMS notification not sent.', [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
            ]);

            return;
        }

        $phoneNumber = $this->getPhoneNumber($notifiable);

        if (empty($phoneNumber)) {
            Log::warning('No phone number found for notifiable. SMS notification not sent.', [
                'notifiable' => get_class($notifiable),
                'notifiable_id' => $notifiable->getKey() ?? null,
            ]);

            return;
        }

        $message = $this->getMessage($notifiable, $notification);

        if (! $message) {
            return;
        }

        try {
            $this->provider->send($phoneNumber, $message);

            Log::info('SMS notification sent successfully.', [
                'to' => $this->maskPhoneNumber($phoneNumber),
                'notification' => get_class($notification),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification.', [
                'to' => $this->maskPhoneNumber($phoneNumber),
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the phone number from the notifiable.
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForSms')) {
            return $notifiable->routeNotificationForSms();
        }

        return $notifiable->phone ?? $notifiable->phone_number ?? null;
    }

    /**
     * Get the SMS message from the notification.
     */
    protected function getMessage($notifiable, Notification $notification): ?SmsMessage
    {
        if (method_exists($notification, 'toSms')) {
            return $notification->toSms($notifiable);
        }

        return null;
    }

    /**
     * Mask phone number for logging purposes.
     */
    protected function maskPhoneNumber(string $phoneNumber): string
    {
        $length = strlen($phoneNumber);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return substr($phoneNumber, 0, 3).str_repeat('*', $length - 6).substr($phoneNumber, -3);
    }
}
