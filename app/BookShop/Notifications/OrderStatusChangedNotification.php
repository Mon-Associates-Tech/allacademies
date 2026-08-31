<?php

namespace App\BookShop\Notifications;

use App\BookShop\Enums\OrderStatus;
use App\BookShop\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChangedNotification extends BookShopNotification
{
    public function __construct(private readonly Order $order, private readonly OrderStatus $previousStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Order {$this->order->order_number} - {$this->order->status->label()}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your order {$this->order->order_number} is now: {$this->order->status->label()}.");

        if ($this->order->status->value === 'cancelled' && $this->order->cancelled_reason) {
            $message->line('Reason: '.$this->order->cancelled_reason);
        }

        if ($this->order->status->value === 'ready') {
            $message->line("It's ready for pickup at {$this->order->branch?->name}.");
        }

        $message->action('View Order', route('bookshop.shop.orders.show', $this->order));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Order {$this->order->order_number} - {$this->order->status->label()}",
            'body' => $this->order->status->value === 'cancelled' && $this->order->cancelled_reason
                ? "Cancelled: {$this->order->cancelled_reason}"
                : "Status changed from {$this->previousStatus->label()} to {$this->order->status->label()}",
            'url' => route('bookshop.shop.orders.show', $this->order),
        ];
    }
}
