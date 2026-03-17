<?php

namespace App\Notifications;

use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollRunSubmittedForApproval extends Notification
{
    use Queueable;

    public function __construct(
        public PayrollRun $run
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payroll Run Awaiting Approval')
            ->line("A payroll run has been submitted for your approval.")
            ->line("Schedule: {$this->run->schedule->name}")
            ->line("Recipients: {$this->run->recipient_count}")
            ->line("Total Amount: GH₵" . number_format($this->run->total_amount, 2))
            ->action('Review Payroll Run', route('payroll.runs.show', $this->run))
            ->line('Please review and approve this payroll run.');
    }

    public function toArray($notifiable): array
    {
        return [
            'run_id' => $this->run->id,
            'schedule_name' => $this->run->schedule->name,
            'recipient_count' => $this->run->recipient_count,
            'total_amount' => $this->run->total_amount,
        ];
    }
}
