<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\RestockRequest;
use Illuminate\Notifications\Messages\MailMessage;

class RestockRequestedNotification extends BookShopNotification
{
    public function __construct(private readonly RestockRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('New Restock Request')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->request->requestedBy?->name} at {$this->request->branch?->name} requested more stock.")
            ->line("Book: {$this->request->book?->title}")
            ->line("Quantity requested: {$this->request->requested_quantity}")
            ->action('Review Request', route('bookshop.staff.restock-requests.index'))
            ->line('Approving debits the warehouse pool and credits the branch immediately.');

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New restock request',
            'body' => "{$this->request->branch?->name} requested {$this->request->requested_quantity}x \"{$this->request->book?->title}\"",
            'url' => route('bookshop.staff.restock-requests.index'),
        ];
    }
}
