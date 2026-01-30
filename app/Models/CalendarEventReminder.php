<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class CalendarEventReminder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'calendar_event_id',
        'user_id',
        'remind_at',
        'minutes_before',
        'channels',
        'is_sent',
        'sent_at',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'sent_at' => 'datetime',
            'is_sent' => 'boolean',
            'channels' => 'array',
        ];
    }

    /**
     * Get the calendar event this reminder belongs to.
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    /**
     * Get the user who should receive this reminder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get pending reminders that are due.
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_sent', false)
            ->where('status', self::STATUS_PENDING)
            ->where('remind_at', '<=', Carbon::now());
    }

    /**
     * Scope to get reminders for a specific event.
     */
    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('calendar_event_id', $eventId);
    }

    /**
     * Scope to get reminders for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get pending reminders.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('is_sent', false);
    }

    /**
     * Check if the reminder has a specific channel enabled.
     */
    public function hasChannel(string|NotificationChannel $channel): bool
    {
        $channelValue = $channel instanceof NotificationChannel ? $channel->value : $channel;

        return in_array($channelValue, $this->channels ?? []);
    }

    /**
     * Get the notification channels as NotificationChannel enums.
     *
     * @return array<NotificationChannel>
     */
    public function getNotificationChannels(): array
    {
        return array_filter(
            array_map(
                fn ($channel) => NotificationChannel::tryFrom($channel),
                $this->channels ?? []
            )
        );
    }

    /**
     * Mark the reminder as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => Carbon::now(),
            'status' => self::STATUS_SENT,
        ]);
    }

    /**
     * Mark the reminder as failed.
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Cancel the reminder.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Check if the reminder is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->is_sent;
    }

    /**
     * Check if the reminder can be sent.
     */
    public function canBeSent(): bool
    {
        return $this->isPending() && $this->remind_at->lte(Carbon::now());
    }

    /**
     * Create a reminder for a calendar event.
     *
     * @param  array<string>  $channels
     */
    public static function createForEvent(
        CalendarEvent $event,
        int $userId,
        int $minutesBefore = 15,
        array $channels = ['email', 'database']
    ): self {
        $remindAt = $event->start_date->copy()->subMinutes($minutesBefore);

        return self::create([
            'calendar_event_id' => $event->id,
            'user_id' => $userId,
            'remind_at' => $remindAt,
            'minutes_before' => $minutesBefore,
            'channels' => $channels,
            'status' => self::STATUS_PENDING,
        ]);
    }
}
