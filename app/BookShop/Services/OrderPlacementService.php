<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\FulfillmentMethod;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\Book;
use App\BookShop\Models\Branch;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use App\BookShop\Models\OrderItem;
use App\BookShop\Models\Staff;
use App\BookShop\Notifications\LowStockNotification;
use App\BookShop\Notifications\OrderPlacedCustomerNotification;
use App\BookShop\Notifications\OrderPlacedStaffNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderPlacementService
{
    /**
     * @param  array<int, int>  $items  book_id => quantity
     *
     * @throws OrderPlacementException
     */
    public function place(
        Customer $customer,
        Branch $branch,
        array $items,
        ?string $notes = null,
        FulfillmentMethod $fulfillmentMethod = FulfillmentMethod::PICKUP,
        ?string $deliveryAddress = null,
    ): Order {
        $items = array_filter($items, fn ($quantity) => (int) $quantity > 0);

        if (empty($items)) {
            throw new OrderPlacementException('Select at least one book and quantity to place an order.');
        }

        if (! $branch->is_active) {
            throw new OrderPlacementException('That branch is no longer accepting orders. Please choose a different one.');
        }

        if ($fulfillmentMethod === FulfillmentMethod::DELIVERY && empty(trim((string) $deliveryAddress))) {
            throw new OrderPlacementException('A delivery address is required for delivery orders.');
        }

        // Notifications are dispatched AFTER the transaction commits, not
        // from inside it - queued jobs could otherwise pick the
        // notification up before the transaction is durable, or worse,
        // fire for an order that later gets rolled back by an exception
        // elsewhere in the same request. $lowStockLevels is collected
        // inside the transaction (decrement() already syncs the in-memory
        // quantity, no extra query needed) and used for alerts afterward.
        [$order, $lowStockLevels] = DB::transaction(function () use ($customer, $branch, $items, $notes, $fulfillmentMethod, $deliveryAddress) {
            $books = Book::query()->active()->whereIn('id', array_keys($items))->get()->keyBy('id');

            // Lock the relevant stock rows for the duration of the
            // transaction so two simultaneous orders can't both succeed
            // against the same last few units.
            $stockLevels = BranchStockLevel::query()
                ->where('branch_id', $branch->id)
                ->whereIn('book_id', array_keys($items))
                ->lockForUpdate()
                ->get()
                ->keyBy('book_id');

            $subtotal = 0;
            $lineItems = [];

            foreach ($items as $bookId => $quantity) {
                $book = $books->get($bookId);

                if (! $book) {
                    throw new OrderPlacementException("One of the selected books is no longer available.");
                }

                $stock = $stockLevels->get($bookId);
                $available = $stock?->quantity ?? 0;

                if ($available < $quantity) {
                    throw new OrderPlacementException(
                        "Only {$available} copies of \"{$book->title}\" are available at ".$branch->name.'.'
                    );
                }

                $lineTotal = round($book->price * $quantity, 2);
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'book' => $book,
                    'stock' => $stock,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'subtotal' => $subtotal,
                'notes' => $notes,
                'fulfillment_method' => $fulfillmentMethod,
                'delivery_address' => $fulfillmentMethod === FulfillmentMethod::DELIVERY ? $deliveryAddress : null,
            ]);

            $lowStockLevels = [];

            foreach ($lineItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item['book']->id,
                    'title_snapshot' => $item['book']->title,
                    'author_snapshot' => $item['book']->author,
                    'unit_price' => $item['book']->price,
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);

                $item['stock']->decrement('quantity', $item['quantity']);

                if ($item['stock']->isLowStock()) {
                    $lowStockLevels[] = $item['stock'];
                }
            }

            return [$order->fresh('items'), $lowStockLevels];
        });

        $this->sendNotifications($order, $branch, $customer, $lowStockLevels);

        return $order;
    }

    private function sendNotifications(Order $order, Branch $branch, Customer $customer, array $lowStockLevels): void
    {
        $customer->notify(new OrderPlacedCustomerNotification($order));

        $branchStaff = Staff::where('branch_id', $branch->id)->where('is_active', true)->get();

        if ($branchStaff->isNotEmpty()) {
            Notification::send($branchStaff, new OrderPlacedStaffNotification($order));

            foreach ($lowStockLevels as $stockLevel) {
                Notification::send($branchStaff, new LowStockNotification($stockLevel));
            }
        }
    }
}
