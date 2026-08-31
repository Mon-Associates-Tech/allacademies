<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\OrderStatus;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Order;
use App\BookShop\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    /**
     * @throws OrderPlacementException
     */
    public function transition(Order $order, OrderStatus $target, ?string $reason = null): Order
    {
        if (! $order->status->canTransitionTo($target)) {
            throw new OrderPlacementException(
                "Cannot move an order from \"{$order->status->label()}\" to \"{$target->label()}\"."
            );
        }

        // Payment is required before fulfillment starts, but an unpaid or
        // abandoned order can still always be cancelled (which restocks
        // it) - staff need that escape valve regardless of payment state.
        if ($target !== OrderStatus::CANCELLED && ! $order->isPaid()) {
            throw new OrderPlacementException(
                "This order hasn't been paid for yet ({$order->payment_status->label()}) - it can't move to \"{$target->label()}\" until payment is confirmed."
            );
        }

        $previousStatus = $order->status;

        $updated = DB::transaction(function () use ($order, $target, $reason) {
            if ($target === OrderStatus::CANCELLED) {
                $this->restock($order);
            }

            $order->update([
                'status' => $target,
                'cancelled_reason' => $target === OrderStatus::CANCELLED ? $reason : $order->cancelled_reason,
                'cancelled_at' => $target === OrderStatus::CANCELLED ? now() : $order->cancelled_at,
                'completed_at' => $target === OrderStatus::COMPLETED ? now() : $order->completed_at,
            ]);

            return $order->fresh('items');
        });

        $updated->customer?->notify(new OrderStatusChangedNotification($updated, $previousStatus));

        return $updated;
    }

    /**
     * Returns every line item's quantity back to branch stock. Called when
     * an order is cancelled at any point before completion — pending,
     * processing, or ready all still had stock reserved against them.
     */
    private function restock(Order $order): void
    {
        foreach ($order->items()->with('book')->get() as $item) {
            if (! $item->book_id) {
                continue; // catalog entry was deleted since the order was placed
            }

            BranchStockLevel::query()
                ->where('branch_id', $order->branch_id)
                ->where('book_id', $item->book_id)
                ->increment('quantity', $item->quantity);
        }
    }
}
