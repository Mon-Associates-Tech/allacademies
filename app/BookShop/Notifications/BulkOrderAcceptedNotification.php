<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\BulkOrderRequest;
use App\BookShop\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;

class BulkOrderAcceptedNotification extends BookShopNotification
{
    public function __construct(
        private readonly BulkOrderRequest $request,
        private readonly Order $order,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Bulk Order Accepted - {$this->request->institution_name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->request->customer?->name} accepted the quote for {$this->request->institution_name} (request {$this->request->request_number}).")
            ->line("It's now order {$this->order->order_number} and needs payment before fulfillment, same as any other order.")
            ->action('View Order', route('bookshop.staff.orders.show', $this->order));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Bulk order accepted - {$this->request->institution_name}",
            'body' => "Now order {$this->order->order_number}",
            'url' => route('bookshop.staff.orders.show', $this->order),
        ];
    }
}
