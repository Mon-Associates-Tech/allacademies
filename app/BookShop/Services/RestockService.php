<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\RestockRequestStatus;
use App\BookShop\Enums\StaffRole;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Models\Book;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\RestockRequest;
use App\BookShop\Models\Staff;
use App\BookShop\Models\WarehouseStock;
use App\BookShop\Notifications\RestockRequestedNotification;
use App\BookShop\Notifications\RestockReviewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Reuses OrderPlacementException for restock failures too — both are
 * "expected, user-facing, show-the-message-directly" cases, not distinct
 * enough to warrant a second exception class.
 */
class RestockService
{
    public function createRequest(Staff $branchAdmin, Book $book, int $quantity, ?string $notes = null): RestockRequest
    {
        if ($branchAdmin->isSuperAdmin() || ! $branchAdmin->branch_id) {
            throw new OrderPlacementException('Only a staff member assigned to a branch can request stock.');
        }

        if ($quantity < 1) {
            throw new OrderPlacementException('Requested quantity must be at least 1.');
        }

        $request = RestockRequest::create([
            'branch_id' => $branchAdmin->branch_id,
            'book_id' => $book->id,
            'requested_quantity' => $quantity,
            'requested_by_staff_id' => $branchAdmin->id,
            'notes' => $notes,
        ]);

        $superadmins = Staff::where('role', StaffRole::SUPERADMIN)->where('is_active', true)->get();

        if ($superadmins->isNotEmpty()) {
            Notification::send($superadmins, new RestockRequestedNotification($request));
        }

        return $request;
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
