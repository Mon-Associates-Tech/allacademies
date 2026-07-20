<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\RestockRequestStatus;
use App\BookShop\Enums\StaffRole;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\RestockRequest;
use App\BookShop\Models\Staff;
use App\BookShop\Models\WarehouseStock;
use App\BookShop\Notifications\RestockBatchRequestedNotification;
use App\BookShop\Notifications\RestockReviewedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Reuses OrderPlacementException for restock failures too — both are
 * "expected, user-facing, show-the-message-directly" cases, not distinct
 * enough to warrant a second exception class.
 */
class RestockService
{
    /**
     * Creates one RestockRequest row per item (each stays independently
     * approvable/rejectable - a superadmin might approve some and reject
     * others from the same batch), but notifies superadmins once with a
     * summary rather than once per item, so a 5-item batch doesn't
     * generate 5 separate emails.
     *
     * @param  iterable<array{book_id: int, quantity: int}>  $items
     * @return Collection<int, RestockRequest>
     *
     * @throws OrderPlacementException
     */
    public function createBatch(Staff $branchAdmin, iterable $items, ?string $notes = null): Collection
    {
        if ($branchAdmin->isSuperAdmin() || ! $branchAdmin->branch_id) {
            throw new OrderPlacementException('Only a staff member assigned to a branch can request stock.');
        }

        $items = collect($items)->filter(fn ($row) => ! empty($row['book_id']) && (int) ($row['quantity'] ?? 0) > 0);

        if ($items->isEmpty()) {
            throw new OrderPlacementException('Add at least one book and quantity.');
        }

        $requests = DB::transaction(function () use ($branchAdmin, $items, $notes) {
            return $items->map(fn ($row) => RestockRequest::create([
                'branch_id' => $branchAdmin->branch_id,
                'book_id' => $row['book_id'],
                'requested_quantity' => (int) $row['quantity'],
                'requested_by_staff_id' => $branchAdmin->id,
                'notes' => $notes,
            ]));
        });

        $superadmins = Staff::where('role', StaffRole::SUPERADMIN)->where('is_active', true)->get();

        if ($superadmins->isNotEmpty()) {
            Notification::send($superadmins, new RestockBatchRequestedNotification($requests));
        }

        return $requests;
    }

    /**
     * Approving debits the warehouse pool and credits the branch's stock
     * in the same transaction — per the earlier decision to combine
     * "simple approve" with "real stock ledger" rather than having a
     * separate unfulfilled-approved state.
     *
     * @throws OrderPlacementException
     */
    public function approve(Staff $superadmin, RestockRequest $request): RestockRequest
    {
        $this->ensurePending($request);

        $updated = DB::transaction(function () use ($superadmin, $request) {
            $warehouseStock = WarehouseStock::query()
                ->where('book_id', $request->book_id)
                ->lockForUpdate()
                ->first();

            $available = $warehouseStock?->quantity ?? 0;

            if ($available < $request->requested_quantity) {
                throw new OrderPlacementException(
                    "Warehouse only has {$available} units of \"{$request->book->title}\" — restock the warehouse first, or approve a smaller quantity by editing the request."
                );
            }

            $warehouseStock->decrement('quantity', $request->requested_quantity);

            $branchStock = BranchStockLevel::query()
                ->firstOrCreate(
                    ['branch_id' => $request->branch_id, 'book_id' => $request->book_id],
                    ['quantity' => 0, 'low_stock_threshold' => 5]
                );
            $branchStock->increment('quantity', $request->requested_quantity);

            $request->update([
                'status' => RestockRequestStatus::APPROVED,
                'reviewed_by_staff_id' => $superadmin->id,
                'reviewed_at' => now(),
            ]);

            return $request->fresh();
        });

        $updated->requestedBy?->notify(new RestockReviewedNotification($updated));

        return $updated;
    }

    public function reject(Staff $superadmin, RestockRequest $request, string $reason): RestockRequest
    {
        $this->ensurePending($request);

        $request->update([
            'status' => RestockRequestStatus::REJECTED,
            'reviewed_by_staff_id' => $superadmin->id,
            'reviewed_at' => now(),
            'reason' => $reason,
        ]);

        $updated = $request->fresh();
        $updated->requestedBy?->notify(new RestockReviewedNotification($updated));

        return $updated;
    }

    /**
     * @throws OrderPlacementException
     */
    private function ensurePending(RestockRequest $request): void
    {
        if (! $request->isPending()) {
            throw new OrderPlacementException('This request has already been reviewed.');
        }
    }
}
