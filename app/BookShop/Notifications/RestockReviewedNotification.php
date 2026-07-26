<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\RestockRequest;
use Illuminate\Notifications\Messages\MailMessage;

class RestockReviewedNotification extends BookShopNotification
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
        $approved = $this->request->status->value === 'approved';

        $message = (new MailMessage)
            ->subject('Restock Request '.($approved ? 'Approved' : 'Rejected'))
            ->greeting("Hi {$notifiable->name},")
            ->line("Your request for {$this->request->requested_quantity}x \"{$this->request->book?->title}\" was ".($approved ? 'approved.' : 'rejected.'));

        if ($approved) {
            $message->line("Stock has been added to {$this->request->branch?->name}.")
                ->action('View Stock', route('bookshop.staff.stock.index'));
        } else {
            $message->line('Reason: '.($this->request->reason ?: 'No reason given.'))
                ->action('View Request', route('bookshop.staff.restock-requests.show', $this->request->batch_id));
        }

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        $approved = $this->request->status->value === 'approved';

        return [
            'title' => 'Restock request '.($approved ? 'approved' : 'rejected'),
            'body' => "\"{$this->request->book?->title}\" x{$this->request->requested_quantity}"
                .($approved ? '' : ' - '.($this->request->reason ?: 'No reason given')),
            'url' => $approved
                ? route('bookshop.staff.stock.index')
                : route('bookshop.staff.restock-requests.show', $this->request->batch_id),
        ];
    }
}
