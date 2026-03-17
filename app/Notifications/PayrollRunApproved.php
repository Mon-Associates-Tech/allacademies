<?php

namespace App\Notifications;

use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollRunApproved extends Notification
{
    use Queueable;

    public function __construct(public PayrollRun $run) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payroll Run Approved')
            ->line("Your payroll run has been approved and is now processing.")
            ->line("Schedule: {$this->run->schedule->name}")
            ->action('View Payroll Run', route('payroll.runs.show', $this->run));
    }

    public function toArray($notifiable): array
    {
        return [
            'run_id' => $this->run->id,
            'schedule_name' => $this->run->schedule->name,
        ];
    }
}
