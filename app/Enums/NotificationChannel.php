<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case DATABASE = 'database';
    case SMS = 'sms';

    /**
     * Get all available notification channels.
     *
     * @return array<NotificationChannel>
     */
    public static function getAll(): array
    {
        return [
            self::EMAIL,
            self::DATABASE,
            self::SMS,
        ];
    }

    /**
     * Get channels that are currently implemented and active.
     *
     * @return array<NotificationChannel>
     */
    public static function getActiveChannels(): array
    {
        return [
            self::EMAIL,
            self::DATABASE,
            // SMS is not active until a provider is configured
        ];
    }

    /**
     * Get the display label for the channel.
     */
    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::DATABASE => 'In-App Notification',
            self::SMS => 'SMS',
        };
    }

    /**
     * Get the description for the channel.
     */
    public function description(): string
    {
        return match ($this) {
            self::EMAIL => 'Receive notifications via email',
            self::DATABASE => 'Receive notifications in the application',
            self::SMS => 'Receive notifications via SMS text message',
        };
    }

    /**
     * Check if the channel is currently available/configured.
     */
    public function isAvailable(): bool
    {
        return match ($this) {
            self::EMAIL => true,
            self::DATABASE => true,
            self::SMS => config('services.sms.provider') !== null,
        };
    }
}
