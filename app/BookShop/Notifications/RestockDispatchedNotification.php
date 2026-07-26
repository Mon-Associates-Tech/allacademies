<?php

namespace App\BookShop\Notifications;

use App\BookShop\Models\RestockRequest;
use Illuminate\Notifications\Messages\MailMessage;

class RestockDispatchedNotification extends BookShopNotification
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
        $message = (new MailMessage)
            ->subject('Your Restock Is On Its Way')
            ->greeting("Hi {$notifiable->name},")
            ->line("\"{$this->request->book?->title}\" ({$this->request->requested_quantity} units) has been dispatched from the warehouse.")
            ->line('Mark it as delivered once it arrives, then confirm the quantity received.')
            ->action('View Request', route('bookshop.staff.restock-requests.show', $this->request->batch_id));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Restock dispatched',
            'body' => "\"{$this->request->book?->title}\" x{$this->request->requested_quantity} is on its way",
            'url' => route('bookshop.staff.restock-requests.show', $this->request->batch_id),
        ];
    }
}
