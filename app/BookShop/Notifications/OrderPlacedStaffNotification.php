<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedStaffNotification extends BookShopNotification
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
            ->subject("New Order {$this->order->order_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A new order has come in for {$this->order->branch?->name}.")
            ->line('Order: '.$this->order->order_number)
            ->line('Customer: '.$this->order->customer?->name)
            ->line('Items: '.$this->order->items()->count())
            ->line('Total: GHS '.number_format($this->order->subtotal, 2))
            ->action('View Order', route('bookshop.staff.orders.show', $this->order))
            ->line('Please review and update its status once you start processing it.');

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New order {$this->order->order_number}",
            'body' => "{$this->order->customer?->name} placed an order for GHS ".number_format($this->order->subtotal, 2),
            'url' => route('bookshop.staff.orders.show', $this->order),
        ];
    }
}
