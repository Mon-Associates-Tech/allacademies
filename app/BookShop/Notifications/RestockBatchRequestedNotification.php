<?php

namespace App\BookShop\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class RestockBatchRequestedNotification extends BookShopNotification
{
    /**
     * @param  Collection<int, \App\BookShop\Models\RestockRequest>  $requests
     */
    public function __construct(private readonly Collection $requests)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $first = $this->requests->first();
        $count = $this->requests->count();

        $message = (new MailMessage)
            ->subject('New Restock Request ('.$count.' item'.($count > 1 ? 's' : '').')')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$first->requestedBy?->name} at {$first->branch?->name} requested stock for {$count} book(s):");

        foreach ($this->requests as $request) {
            $message->line("- {$request->book?->title}: {$request->requested_quantity}");
        }

        $message->action('Review Requests', route('bookshop.staff.restock-requests.index'))
            ->line('Approving each item debits the warehouse pool and credits the branch immediately.');

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        $first = $this->requests->first();
        $count = $this->requests->count();

        return [
            'title' => "New restock request ({$count} item".($count > 1 ? 's' : '').')',
            'body' => "{$first->branch?->name} requested {$count} book(s)",
            'url' => route('bookshop.staff.restock-requests.index'),
        ];
    }
}
