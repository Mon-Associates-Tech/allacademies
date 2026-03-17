<?php

namespace App\Notifications;

use App\Models\PayrollDisbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollDisbursementFailed extends Notification
{
    use Queueable;

    public function __construct(public $subject) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->subject instanceof PayrollDisbursement) {
            return (new MailMessage)
                ->subject('Payroll Disbursement Failed')
                ->line("A payroll disbursement has failed.")
                ->line("Recipient: {$this->subject->payrollEntry->full_name}")
                ->line("Amount: GH₵" . number_format($this->subject->amount, 2))
                ->line("Reason: {$this->subject->failure_reason}")
                ->action('View Details', route('payroll.runs.show', $this->subject->payroll_run_id));
        }
        
        return (new MailMessage)
            ->subject('Payroll Run Failed')
            ->line("A payroll run has failed to process.")
            ->action('View Details', route('payroll.runs.show', $this->subject->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'subject_type' => get_class($this->subject),
            'subject_id' => $this->subject->id,
        ];
    }
}
