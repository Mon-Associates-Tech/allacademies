<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SchoolPaymentCreatedNotification extends Notification
{
    use Queueable;

    public $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $amount = number_format($this->payment->amount / 100, 2) ?: $this->payment->amount;

        return (new MailMessage)
            ->subject('New payment request')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('A new payment has been created for you:')
            ->line($this->payment->description ?? '')
            ->line('Amount: '.($this->payment->amount))
            ->action('View payments', route('students.fees.index'))
            ->line('If you believe this is a mistake, contact the school administrator.');
    }

    /**
     * Get the array representation of the notification for storage in database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'school_payment_created',
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'description' => $this->payment->description ?? null,
            'link' => route('students.fees.index'),
        ];
    }
}
