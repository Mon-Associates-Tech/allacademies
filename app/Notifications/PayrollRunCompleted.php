<?php

namespace App\Notifications;

use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollRunCompleted extends Notification
{
    use Queueable;

    public function __construct(public PayrollRun $run) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $successCount = $this->run->disbursements()->success()->count();
        
        return (new MailMessage)
            ->subject('Payroll Run Completed')
            ->line("Payroll run has been completed.")
            ->line("Schedule: {$this->run->schedule->name}")
            ->line("Successful Payments: {$successCount} of {$this->run->recipient_count}")
            ->action('View Details', route('payroll.runs.show', $this->run));
    }

    public function toArray($notifiable): array
    {
        return [
            'run_id' => $this->run->id,
            'schedule_name' => $this->run->schedule->name,
        ];
    }
}
