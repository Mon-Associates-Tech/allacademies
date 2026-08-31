<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\Staff;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Carries the plaintext password at construction time only - never
 * persisted anywhere except this transient notification payload, which
 * is why this is mail-only (no 'database' channel): storing a plaintext
 * password inside a database notification row would be a real problem
 * long after the email itself has been read and forgotten.
 */
class StaffAccountCreatedNotification extends BookShopNotification
{
    public function __construct(private readonly Staff $staff, private readonly string $plaintextPassword)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Your BookShop Staff Account')
            ->greeting("Hi {$this->staff->name},")
            ->line('An account has been created for you on BookShop.')
            ->line('Email: '.$this->staff->email)
            ->line('Temporary password: '.$this->plaintextPassword)
            ->line("You'll be asked to change this password when you first sign in.")
            ->action('Sign In', route('bookshop.staff.login'));

        return $this->applyFrom($message);
    }
}
