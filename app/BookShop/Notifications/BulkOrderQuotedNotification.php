<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\BulkOrderRequest;
use Illuminate\Notifications\Messages\MailMessage;

class BulkOrderQuotedNotification extends BookShopNotification
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
        $quotedItems = $this->request->items->whereNotNull('quoted_unit_price');
        $total = $quotedItems->sum(fn ($item) => $item->quotedLineTotal());

        $message = (new MailMessage)
            ->subject("Quote Ready - {$this->request->request_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your bulk order request for {$this->request->institution_name} has been quoted:");

        foreach ($quotedItems as $item) {
            $qty = $item->quoted_quantity ?? $item->requested_quantity;
            $message->line("- {$item->title_snapshot}: {$qty} x GHS ".number_format((float) $item->quoted_unit_price, 2)." = GHS ".number_format($item->quotedLineTotal(), 2));
        }

        $unquoted = $this->request->items->whereNull('quoted_unit_price');
        if ($unquoted->isNotEmpty()) {
            $message->line("Note: {$unquoted->count()} item(s) from your request could not be quoted and are not included.");
        }

        $message->line('Total: GHS '.number_format($total, 2));

        if ($this->request->staff_notes) {
            $message->line('Note from the branch: '.$this->request->staff_notes);
        }

        $message->action('Review & Accept Quote', route('bookshop.shop.bulk-orders.show', $this->request))
            ->line('This quote is held against current stock, but not reserved until you accept it.');

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        $quotedItems = $this->request->items->whereNotNull('quoted_unit_price');
        $total = $quotedItems->sum(fn ($item) => $item->quotedLineTotal());

        return [
            'title' => "Quote ready - {$this->request->request_number}",
            'body' => 'Total: GHS '.number_format($total, 2),
            'url' => route('bookshop.shop.bulk-orders.show', $this->request),
        ];
    }
}
