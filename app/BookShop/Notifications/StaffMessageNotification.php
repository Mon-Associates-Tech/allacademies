<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\Staff;
use Illuminate\Notifications\Messages\MailMessage;

class StaffMessageNotification extends BookShopNotification
{
    public function __construct(
        private readonly Staff $sender,
        private readonly string $subject,
        private readonly string $body,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->subject)
            ->greeting("Hi {$notifiable->name},")
            ->line($this->body)
            ->salutation('— '.$this->sender->name.($this->sender->branch ? ', '.$this->sender->branch->name : ', BookShop'));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->subject,
            'body' => \Illuminate\Support\Str::limit($this->body, 100),
            'url' => route('bookshop.shop.catalog'),
        ];
    }
}
