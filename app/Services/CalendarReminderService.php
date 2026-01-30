<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\CalendarEventReminder;
use App\Models\User;
use App\Notifications\CalendarEventReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CalendarReminderService
{
    /**
     * Default reminder times in minutes before event.
     */
    public const DEFAULT_REMINDER_TIMES = [15, 60, 1440]; // 15 min, 1 hour, 1 day

    /**
     * Default notification channels.
     */
    public const DEFAULT_CHANNELS = ['email', 'database'];

    /**
     * Create a reminder for a calendar event.
     *
     * @param  array<string>  $channels
     */
    public function createReminder(
        CalendarEvent $event,
        User $user,
        int $minutesBefore = 15,
        array $channels = self::DEFAULT_CHANNELS
    ): CalendarEventReminder {
        // Check if a similar reminder already exists
        $existingReminder = CalendarEventReminder::query()
            ->forEvent($event->id)
            ->forUser($user->id)
            ->where('minutes_before', $minutesBefore)
            ->pending()
            ->first();

        if ($existingReminder) {
            // Update channels if reminder exists
            $existingReminder->update(['channels' => $channels]);

            return $existingReminder;
        }

        return CalendarEventReminder::createForEvent($event, $user->id, $minutesBefore, $channels);
    }

    /**
     * Create multiple reminders for an event.
     *
     * @param  array<int>  $minutesBeforeList
     * @param  array<string>  $channels
     * @return Collection<CalendarEventReminder>
     */
    public function createReminders(
        CalendarEvent $event,
        User $user,
        array $minutesBeforeList = self::DEFAULT_REMINDER_TIMES,
        array $channels = self::DEFAULT_CHANNELS
    ): Collection {
        $reminders = collect();

        foreach ($minutesBeforeList as $minutesBefore) {
            // Only create reminder if the remind_at time is in the future
            $remindAt = $event->start_date->copy()->subMinutes($minutesBefore);

            if ($remindAt->isFuture()) {
                $reminders->push(
                    $this->createReminder($event, $user, $minutesBefore, $channels)
                );
            }
        }

        return $reminders;
    }

    /**
     * Cancel all pending reminders for an event.
     */
    public function cancelRemindersForEvent(CalendarEvent $event): int
    {
        return CalendarEventReminder::query()
            ->forEvent($event->id)
            ->pending()
            ->update(['status' => CalendarEventReminder::STATUS_CANCELLED]);
    }

    /**
     * Cancel all pending reminders for a user on an event.
     */
    public function cancelUserReminders(CalendarEvent $event, User $user): int
    {
        return CalendarEventReminder::query()
            ->forEvent($event->id)
            ->forUser($user->id)
            ->pending()
            ->update(['status' => CalendarEventReminder::STATUS_CANCELLED]);
    }

    /**
     * Process and send all due reminders.
     */
    public function processDueReminders(): array
    {
        $stats = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
        ];

        $dueReminders = CalendarEventReminder::query()
            ->due()
            ->with(['calendarEvent', 'user'])
            ->get();

        foreach ($dueReminders as $reminder) {
            $stats['processed']++;

            try {
                $this->sendReminder($reminder);
                $stats['sent']++;
            } catch (\Exception $e) {
                $stats['failed']++;
                Log::error('Failed to send calendar reminder', [
                    'reminder_id' => $reminder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    /**
     * Send a single reminder notification.
     */
    public function sendReminder(CalendarEventReminder $reminder): void
    {
        $event = $reminder->calendarEvent;
        $user = $reminder->user;

        if (! $event || ! $user) {
            $reminder->markAsFailed('Event or user not found');

            return;
        }

        // Check if event hasn't been deleted or cancelled
        if ($event->trashed ?? false) {
            $reminder->markAsFailed('Event has been deleted');

            return;
        }

        try {
            $user->notify(new CalendarEventReminderNotification($event, $reminder));
            $reminder->markAsSent();

            Log::info('Calendar reminder sent', [
                'reminder_id' => $reminder->id,
                'event_id' => $event->id,
                'user_id' => $user->id,
                'channels' => $reminder->channels,
            ]);
        } catch (\Exception $e) {
            $reminder->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    /**
     * Get pending reminders for a user.
     *
     * @return Collection<CalendarEventReminder>
     */
    public function getPendingRemindersForUser(User $user): Collection
    {
        return CalendarEventReminder::query()
            ->forUser($user->id)
            ->pending()
            ->with('calendarEvent')
            ->orderBy('remind_at')
            ->get();
    }

    /**
     * Get reminders for a specific event.
     *
     * @return Collection<CalendarEventReminder>
     */
    public function getRemindersForEvent(CalendarEvent $event): Collection
    {
        return CalendarEventReminder::query()
            ->forEvent($event->id)
            ->with('user')
            ->orderBy('remind_at')
            ->get();
    }

    /**
     * Update reminder channels.
     *
     * @param  array<string>  $channels
     */
    public function updateReminderChannels(CalendarEventReminder $reminder, array $channels): CalendarEventReminder
    {
        $reminder->update(['channels' => $channels]);

        return $reminder->fresh();
    }

    /**
     * Reschedule a reminder when event time changes.
     */
    public function rescheduleReminder(CalendarEventReminder $reminder, CalendarEvent $event): CalendarEventReminder
    {
        $newRemindAt = $event->start_date->copy()->subMinutes($reminder->minutes_before);

        $reminder->update([
            'remind_at' => $newRemindAt,
            'is_sent' => false,
            'status' => CalendarEventReminder::STATUS_PENDING,
            'sent_at' => null,
            'failure_reason' => null,
        ]);

        return $reminder->fresh();
    }

    /**
     * Reschedule all reminders for an event when its time changes.
     */
    public function rescheduleEventReminders(CalendarEvent $event): int
    {
        $reminders = CalendarEventReminder::query()
            ->forEvent($event->id)
            ->pending()
            ->get();

        $count = 0;
        foreach ($reminders as $reminder) {
            $this->rescheduleReminder($reminder, $event);
            $count++;
        }

        return $count;
    }
}
