<?php

namespace App\Notifications;

use App\Models\PayrollSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduledPayrollDue extends Notification
{
    use Queueable;

    public function __construct(public PayrollSchedule $schedule) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Scheduled Payroll Due')
            ->line("A scheduled payroll is due for processing.")
            ->line("Schedule: {$this->schedule->name}")
            ->line("Frequency: {$this->schedule->frequency}")
            ->action('Create Payroll Run', route('payroll.runs.create', ['schedule' => $this->schedule->id]))
            ->line('Please review and submit the payroll run for approval.');
    }

    public function toArray($notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'schedule_name' => $this->schedule->name,
        ];
    }
}
