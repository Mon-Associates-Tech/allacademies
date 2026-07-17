<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\OrderStatus;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Order;
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

        return DB::transaction(function () use ($order, $target, $reason) {
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
