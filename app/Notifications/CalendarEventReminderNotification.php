<?php

namespace App\Notifications;

use App\Channels\Messages\SmsMessage;
use App\Channels\SmsChannel;
use App\Models\CalendarEvent;
use App\Models\CalendarEventReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CalendarEventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CalendarEvent $event,
        public CalendarEventReminder $reminder
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        foreach ($this->reminder->channels ?? [] as $channel) {
            $channels[] = match ($channel) {
                'email' => 'mail',
                'database' => 'database',
                'sms' => SmsChannel::class,
                default => null,
            };
        }

        return array_filter($channels);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $startTime = $this->event->all_day
            ? $this->event->start_date->format('l, F j, Y')
            : $this->event->start_date->format('l, F j, Y \a\t g:i A');

        if ($this->reminder->minutes_before >= 60) {
            $hours = floor($this->reminder->minutes_before / 60);
            $timeText = $hours.' '.($hours === 1 ? 'hour' : 'hours');
        } else {
            $timeText = $this->reminder->minutes_before.' minutes';
        }

        // Get event URL if available
        $eventUrl = null;
        if ($this->event->event && method_exists($this->event->event, 'getCalendarEventUrl')) {
            $eventUrl = $this->event->event->getCalendarEventUrl();
        }

        return (new MailMessage)
            ->subject('Reminder: '.$this->event->title)
            ->view('emails.calendar-event-reminder', [
                'event' => $this->event,
                'reminder' => $this->reminder,
                'notifiable' => $notifiable,
                'timeText' => $timeText,
                'startTime' => $startTime,
                'eventUrl' => $eventUrl,
            ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'calendar_event_reminder',
            'calendar_event_id' => $this->event->id,
            'reminder_id' => $this->reminder->id,
            'event_title' => $this->event->title,
            'event_description' => $this->event->description,
            'event_start_date' => $this->event->start_date,
            'event_end_date' => $this->event->end_date,
            'event_all_day' => $this->event->all_day,
            'event_type' => $this->event->event_type_name,
            'minutes_before' => $this->reminder->minutes_before,
            'color' => $this->event->display_color,
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): SmsMessage
    {
        $startTime = $this->event->all_day
            ? $this->event->start_date->format('M d')
            : $this->event->start_date->format('M d, g:i A');

        if ($this->reminder->minutes_before >= 60) {
            $hours = floor($this->reminder->minutes_before / 60);
            $timeText = $hours.'h';
        } else {
            $timeText = $this->reminder->minutes_before.'min';
        }

        $content = "Reminder: {$this->event->title} starts in {$timeText} ({$startTime})";

        return (new SmsMessage)->content($content);
    }
}
