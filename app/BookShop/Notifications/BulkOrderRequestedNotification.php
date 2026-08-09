<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\BulkOrderRequest;
use Illuminate\Notifications\Messages\MailMessage;

class BulkOrderRequestedNotification extends BookShopNotification
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
        $itemCount = $this->request->items->count();
        $totalQuantity = $this->request->items->sum('requested_quantity');

        $message = (new MailMessage)
            ->subject('New Bulk Order Request')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->request->customer?->name} submitted a bulk order request on behalf of {$this->request->institution_name}.")
            ->line("{$itemCount} book(s), {$totalQuantity} total copies.");

        foreach ($this->request->items as $item) {
            $message->line("- {$item->title_snapshot}: {$item->requested_quantity}");
        }

        if ($this->request->notes) {
            $message->line('Note: '.$this->request->notes);
        }

        $message->action('Review Request', route('bookshop.staff.bulk-orders.show', $this->request));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Bulk order request from {$this->request->institution_name}",
            'body' => $this->request->items->count().' book(s), '.$this->request->items->sum('requested_quantity').' total copies',
            'url' => route('bookshop.staff.bulk-orders.show', $this->request),
        ];
    }
}
