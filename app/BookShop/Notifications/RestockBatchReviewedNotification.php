<?php

namespace App\BookShop\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class RestockBatchReviewedNotification extends BookShopNotification
{
    /**
     * @param  Collection<int, \App\BookShop\Models\RestockRequest>  $processed  successfully approved or rejected
     * @param  Collection<int, array{request: \App\BookShop\Models\RestockRequest, error: string}>  $failed  approve attempts that hit an error (e.g. insufficient warehouse stock) and are still pending
     */
    public function __construct(
        private readonly Collection $processed,
        private readonly Collection $failed,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approvedCount = $this->processed->filter(fn ($r) => $r->status->value === 'approved')->count();
        $rejectedCount = $this->processed->filter(fn ($r) => $r->status->value === 'rejected')->count();

        $message = (new MailMessage)
            ->subject('Your Restock Request Was Reviewed')
            ->greeting("Hi {$notifiable->name},");

        if ($approvedCount > 0) {
            $message->line("{$approvedCount} item(s) approved and added to your branch's stock:");
            foreach ($this->processed->where('status.value', 'approved') as $request) {
                $message->line("- {$request->book?->title}: {$request->requested_quantity}");
            }
        }

        if ($rejectedCount > 0) {
            $message->line("{$rejectedCount} item(s) rejected:");
            foreach ($this->processed->where('status.value', 'rejected') as $request) {
                $message->line("- {$request->book?->title}".($request->reason ? " ({$request->reason})" : ''));
            }
        }

        if ($this->failed->isNotEmpty()) {
            $message->line("{$this->failed->count()} item(s) could not be approved and remain pending:");
            foreach ($this->failed as $failure) {
                $message->line("- {$failure['request']->book?->title}: {$failure['error']}");
            }
        }

        $message->action('View Request', route('bookshop.staff.restock-requests.show', $this->batchId()));

        return $this->applyFrom($message);
    }

    public function toArray(object $notifiable): array
    {
        $approvedCount = $this->processed->filter(fn ($r) => $r->status->value === 'approved')->count();
        $rejectedCount = $this->processed->filter(fn ($r) => $r->status->value === 'rejected')->count();

        $parts = array_filter([
            $approvedCount > 0 ? "{$approvedCount} approved" : null,
            $rejectedCount > 0 ? "{$rejectedCount} rejected" : null,
            $this->failed->isNotEmpty() ? "{$this->failed->count()} still pending" : null,
        ]);

        return [
            'title' => 'Restock request reviewed',
            'body' => implode(', ', $parts),
            'url' => route('bookshop.staff.restock-requests.show', $this->batchId()),
        ];
    }

    private function batchId(): ?string
    {
        return $this->processed->first()?->batch_id ?? $this->failed->first()['request']->batch_id ?? null;
    }
}
