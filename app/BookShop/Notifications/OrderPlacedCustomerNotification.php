<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedCustomerNotification extends BookShopNotification
{
    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Order Confirmed - {$this->order->order_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your order has been placed and will be prepared by {$this->order->branch?->name}.")
            ->line('Order: '.$this->order->order_number)
            ->line('Total: GHS '.number_format($this->order->subtotal, 2))
            ->action('View Order', route('bookshop.shop.orders.show', $this->order))
            ->line("We'll let you know as its status changes.");

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Order {$this->order->order_number} placed",
            'body' => 'Total GHS '.number_format($this->order->subtotal, 2)." - served by {$this->order->branch?->name}",
            'url' => route('bookshop.shop.orders.show', $this->order),
        ];
    }
}
