<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\BulkOrderRequest;
use Illuminate\Notifications\Messages\MailMessage;

class BulkOrderRejectedNotification extends BookShopNotification
{
    public function __construct(private readonly BulkOrderRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Bulk Order Request Update - {$this->request->request_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your bulk order request for {$this->request->institution_name} could not be fulfilled.")
            ->line('Reason: '.($this->request->rejection_reason ?: 'No reason given.'))
            ->action('View Request', route('bookshop.shop.bulk-orders.show', $this->request));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Bulk request rejected - {$this->request->request_number}",
            'body' => $this->request->rejection_reason ?: 'No reason given',
            'url' => route('bookshop.shop.bulk-orders.show', $this->request),
        ];
    }
}
