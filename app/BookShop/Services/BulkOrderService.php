<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\BulkOrderRequestStatus;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\Book;
use App\BookShop\Models\Branch;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\BulkOrderRequest;
use App\BookShop\Models\BulkOrderRequestItem;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use App\BookShop\Models\OrderItem;
use App\BookShop\Models\Staff;
use App\BookShop\Notifications\BulkOrderAcceptedNotification;
use App\BookShop\Notifications\BulkOrderQuotedNotification;
use App\BookShop\Notifications\BulkOrderRejectedNotification;
use App\BookShop\Notifications\BulkOrderRequestedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BulkOrderService
{
    /**
     * Below this total quantity, the regular cart already handles it
     * fine - the bulk flow exists specifically for volume large enough
     * that staff likely want to review/quote before committing branch
     * stock to it. Soft threshold, not a hard technical limit; easy to
     * adjust if it turns out to be set wrong in practice.
     */
    public const MINIMUM_TOTAL_QUANTITY = 10;

    /**
     * @param  array<int, int>  $items  book_id => quantity
     *
     * @throws OrderPlacementException
     */
    public function submit(
        Customer $customer,
        Branch $branch,
        array $items,
        string $institutionName,
        string $institutionType,
        ?string $contactPhone,
        ?string $requestedDeliveryDate,
        ?string $notes,
    ): BulkOrderRequest {
        $items = array_filter($items, fn ($quantity) => (int) $quantity > 0);

        if (empty($items)) {
            throw new OrderPlacementException('Add at least one book and quantity to your bulk request.');
        }

        $totalQuantity = array_sum($items);
        if ($totalQuantity < self::MINIMUM_TOTAL_QUANTITY) {
            throw new OrderPlacementException(
                'Bulk requests need at least '.self::MINIMUM_TOTAL_QUANTITY.' books total (you have '.$totalQuantity.'). '
                .'For smaller orders, use the regular catalog and cart instead.'
            );
        }

        $books = Book::query()->active()->whereIn('id', array_keys($items))->get()->keyBy('id');

        $request = DB::transaction(function () use ($customer, $branch, $items, $books, $institutionName, $institutionType, $contactPhone, $requestedDeliveryDate, $notes) {
            $bulkRequest = BulkOrderRequest::create([
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'institution_name' => $institutionName,
                'institution_type' => $institutionType,
                'contact_phone' => $contactPhone,
                'requested_delivery_date' => $requestedDeliveryDate,
                'notes' => $notes,
            ]);

            foreach ($items as $bookId => $quantity) {
                $book = $books->get($bookId);
                if (! $book) {
                    continue; // deactivated/removed between adding to the builder and submitting
                }

                BulkOrderRequestItem::create([
                    'bulk_order_request_id' => $bulkRequest->id,
                    'book_id' => $book->id,
                    'title_snapshot' => $book->title,
                    'requested_quantity' => $quantity,
                ]);
            }

            return $bulkRequest->fresh('items');
        });

        if ($request->items->isEmpty()) {
            $request->delete();
            throw new OrderPlacementException('None of the selected books are available anymore. Please rebuild your request.');
        }

        $branchStaff = Staff::where('branch_id', $branch->id)->where('is_active', true)->get();
        if ($branchStaff->isNotEmpty()) {
            Notification::send($branchStaff, new BulkOrderRequestedNotification($request));
        }

        return $request;
    }

    /**
     * @param  array<int, array{unit_price: float, quantity: int}>  $itemQuotes  bulk_order_request_item_id => quote
     *
     * @throws OrderPlacementException
     */
    public function quote(Staff $staff, BulkOrderRequest $request, array $itemQuotes, ?string $staffNotes = null): BulkOrderRequest
    {
        if (! $request->isPending()) {
            throw new OrderPlacementException('This request has already been reviewed.');
        }

        $hasAnyQuote = false;

        DB::transaction(function () use ($request, $itemQuotes, &$hasAnyQuote) {
            foreach ($request->items as $item) {
                $quote = $itemQuotes[$item->id] ?? null;
                if (! $quote || empty($quote['unit_price'])) {
                    continue; // staff can quote a subset - unquoted items are implicitly declined
                }

                $item->update([
                    'quoted_unit_price' => $quote['unit_price'],
                    'quoted_quantity' => min((int) ($quote['quantity'] ?? $item->requested_quantity), $item->requested_quantity),
                ]);
                $hasAnyQuote = true;
            }
        });

        if (! $hasAnyQuote) {
            throw new OrderPlacementException('Quote at least one item, or reject the request instead.');
        }

        $request->update([
            'status' => BulkOrderRequestStatus::QUOTED,
            'staff_notes' => $staffNotes,
            'reviewed_by_staff_id' => $staff->id,
            'quoted_at' => now(),
        ]);

        $updated = $request->fresh('items');
        $updated->customer?->notify(new BulkOrderQuotedNotification($updated));

        return $updated;
    }

    public function reject(Staff $staff, BulkOrderRequest $request, string $reason): BulkOrderRequest
    {
        if ($request->status->isTerminal()) {
            throw new OrderPlacementException('This request has already been closed out.');
        }

        $request->update([
            'status' => BulkOrderRequestStatus::REJECTED,
            'reviewed_by_staff_id' => $staff->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $updated = $request->fresh();
        $updated->customer?->notify(new BulkOrderRejectedNotification($updated));

        return $updated;
    }

    public function cancel(BulkOrderRequest $request): BulkOrderRequest
    {
        if ($request->status->isTerminal()) {
            throw new OrderPlacementException('This request can no longer be cancelled.');
        }

        $request->update([
            'status' => BulkOrderRequestStatus::CANCELLED,
            'reviewed_at' => now(),
        ]);

        return $request->fresh();
    }

    /**
     * Converts an accepted quote into a real Order, using the QUOTED
     * price/quantity rather than catalog price - the entire point of the
     * bulk flow. Stock is re-checked and locked here (not assumed from
     * quote time), since time may have passed between the quote and the
     * customer accepting it and branch stock could have moved in the
     * meantime; a shortfall here means the customer needs a fresh quote,
     * not a silently-adjusted order.
     *
     * Deliberately duplicates some of OrderPlacementService's
     * transaction/locking shape rather than calling through it, since
     * bulk pricing bypasses the catalog price entirely - forcing this
     * through the catalog-price-locked path would be more awkward than
     * the small amount of duplication here.
     *
     * @throws OrderPlacementException
     */
    public function acceptQuote(Customer $customer, BulkOrderRequest $request): Order
    {
        if (! $request->isQuoted()) {
            throw new OrderPlacementException('This request does not have an active quote to accept.');
        }

        $quotedItems = $request->items()->whereNotNull('quoted_unit_price')->get();

        if ($quotedItems->isEmpty()) {
            throw new OrderPlacementException('This quote has no priced items.');
        }

        $order = DB::transaction(function () use ($request, $quotedItems, $customer) {
            $stockLevels = BranchStockLevel::query()
                ->where('branch_id', $request->branch_id)
                ->whereIn('book_id', $quotedItems->pluck('book_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('book_id');

            $subtotal = 0;
            $lines = [];

            foreach ($quotedItems as $item) {
                $quantity = $item->quoted_quantity ?? $item->requested_quantity;
                $stock = $stockLevels->get($item->book_id);
                $available = $stock?->quantity ?? 0;

                if ($available < $quantity) {
                    throw new OrderPlacementException(
                        "Stock for \"{$item->title_snapshot}\" has changed since this was quoted (only {$available} left). Please contact the branch for a new quote."
                    );
                }

                $lineTotal = round((float) $item->quoted_unit_price * $quantity, 2);
                $subtotal += $lineTotal;

                $lines[] = ['item' => $item, 'stock' => $stock, 'quantity' => $quantity, 'line_total' => $lineTotal];
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'branch_id' => $request->branch_id,
                'subtotal' => $subtotal,
                'notes' => "Bulk order for {$request->institution_name} (request {$request->request_number})",
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $line['item']->book_id,
                    'title_snapshot' => $line['item']->title_snapshot,
                    'author_snapshot' => $line['item']->book?->author,
                    'unit_price' => $line['item']->quoted_unit_price,
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $line['stock']->decrement('quantity', $line['quantity']);
            }

            $request->update([
                'status' => BulkOrderRequestStatus::CONVERTED,
                'order_id' => $order->id,
                'reviewed_at' => now(),
            ]);

            return $order;
        });

        $branchStaff = Staff::where('branch_id', $request->branch_id)->where('is_active', true)->get();
        if ($branchStaff->isNotEmpty()) {
            Notification::send($branchStaff, new BulkOrderAcceptedNotification($request, $order));
        }

        return $order;
    }
}
