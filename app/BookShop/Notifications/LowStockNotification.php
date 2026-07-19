<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\BranchStockLevel;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends BookShopNotification
{
    public function __construct(private readonly BranchStockLevel $stockLevel)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Low Stock Alert')
            ->greeting("Hi {$notifiable->name},")
            ->line("\"{$this->stockLevel->book?->title}\" is running low at {$this->stockLevel->branch?->name}.")
            ->line("Current quantity: {$this->stockLevel->quantity} (threshold: {$this->stockLevel->low_stock_threshold})")
            ->action('Request Restock', route('bookshop.staff.restock-requests.create'));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Low stock alert',
            'body' => "\"{$this->stockLevel->book?->title}\" at {$this->stockLevel->branch?->name}: {$this->stockLevel->quantity} left",
            'url' => route('bookshop.staff.restock-requests.create'),
        ];
    }
}
