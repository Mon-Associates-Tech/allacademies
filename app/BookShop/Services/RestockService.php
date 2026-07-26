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
use App\BookShop\Notifications\RestockBatchRequestedNotification;
use App\BookShop\Notifications\RestockBatchReviewedNotification;
use App\BookShop\Notifications\RestockDispatchedNotification;
use App\BookShop\Notifications\RestockReviewedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Reuses OrderPlacementException for restock failures too — both are
 * "expected, user-facing, show-the-message-directly" cases, not distinct
 * enough to warrant a second exception class.
 */
class RestockService
{
    /**
     * Creates one RestockRequest row per item, all sharing a batch_id so
     * they can be reviewed together as "one submission" (see
     * RestockRequestController::show()), while each item still stays
     * independently progressable through the lifecycle — a superadmin
     * might approve some and reject others from the same batch.
     *
     * Validates every item against current warehouse availability BEFORE
     * creating anything, all-or-nothing (same convention as
     * OrderPlacementService blocking the whole order if any line item is
     * short) — a branch admin gets one clear list of exactly which books
     * exceed what's available and by how much, rather than half a batch
     * silently going through and the rest failing later at approval time.
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

        $books = Book::query()->with('warehouseStock')->whereIn('id', $items->pluck('book_id'))->get()->keyBy('id');

        $overRequested = [];
        foreach ($items as $row) {
            $book = $books->get($row['book_id']);
            $available = $book?->warehouseStock?->quantity ?? 0;

            if ((int) $row['quantity'] > $available) {
                $overRequested[] = ($book?->title ?? "Book #{$row['book_id']}")." (requested {$row['quantity']}, only {$available} in warehouse)";
            }
        }

        if (! empty($overRequested)) {
            throw new OrderPlacementException(
                'Some quantities exceed what\'s currently in the warehouse: '.implode('; ', $overRequested).
                '. Reduce the quantity or ask the superadmin to restock the warehouse first.'
            );
        }

        $batchId = (string) Str::uuid();

        $requests = DB::transaction(function () use ($branchAdmin, $items, $notes, $batchId) {
            return $items->map(fn ($row) => RestockRequest::create([
                'batch_id' => $batchId,
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
     * Approving reserves the requested quantity from the warehouse pool
     * (decrements it) but does NOT yet credit the branch — that only
     * happens at confirm(), once the branch has actually received the
     * goods. This is the key difference from the old two-status version,
     * where approve() moved stock straight to the branch in one step.
     *
     * $notify defaults to true for the single-item path; approveBatch()
     * below passes false and sends one consolidated summary notification
     * instead of one per item.
     *
     * @throws OrderPlacementException
     */
    public function approve(Staff $superadmin, RestockRequest $request, bool $notify = true): RestockRequest
    {
        $this->ensureCanTransitionTo($request, RestockRequestStatus::APPROVED);

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

            $request->update([
                'status' => RestockRequestStatus::APPROVED,
                'reviewed_by_staff_id' => $superadmin->id,
                'reviewed_at' => now(),
            ]);

            return $request->fresh();
        });

        if ($notify) {
            $updated->requestedBy?->notify(new RestockReviewedNotification($updated));
        }

        return $updated;
    }

    /**
     * Reachable from PENDING (no stock movement yet) or APPROVED (credits
     * the warehouse reservation back, since approve() already debited
     * it) — an approved-but-not-yet-shipped item still needs an escape
     * valve if plans change before it physically leaves the warehouse.
     * Not reachable once DISPATCHED: at that point the shipment is
     * already in motion, so RestockRequestStatus::allowedNextStatuses()
     * simply doesn't offer REJECTED as an option from there.
     *
     * @throws OrderPlacementException
     */
    public function reject(Staff $superadmin, RestockRequest $request, string $reason, bool $notify = true): RestockRequest
    {
        $this->ensureCanTransitionTo($request, RestockRequestStatus::REJECTED);

        $wasReserved = $request->status->hasReservedWarehouseStock();

        $updated = DB::transaction(function () use ($superadmin, $request, $reason, $wasReserved) {
            if ($wasReserved) {
                WarehouseStock::query()
                    ->where('book_id', $request->book_id)
                    ->increment('quantity', $request->requested_quantity);
            }

            $request->update([
                'status' => RestockRequestStatus::REJECTED,
                'reviewed_by_staff_id' => $superadmin->id,
                'reviewed_at' => now(),
                'reason' => $reason,
            ]);

            return $request->fresh();
        });

        if ($notify) {
            $updated->requestedBy?->notify(new RestockReviewedNotification($updated));
        }

        return $updated;
    }

    /**
     * Marks an approved item as physically sent from the warehouse - no
     * stock movement here, the reservation already happened at approve().
     * Superadmin-only (route middleware), same as approve/reject: this
     * models the warehouse team confirming a shipment has left.
     *
     * @throws OrderPlacementException
     */
    public function dispatch(Staff $superadmin, RestockRequest $request): RestockRequest
    {
        $this->ensureCanTransitionTo($request, RestockRequestStatus::DISPATCHED);

        $request->update([
            'status' => RestockRequestStatus::DISPATCHED,
            'dispatched_by_staff_id' => $superadmin->id,
            'dispatched_at' => now(),
        ]);

        $updated = $request->fresh();

        $updated->requestedBy?->notify(new RestockDispatchedNotification($updated));

        return $updated;
    }

    /**
     * Marks a dispatched item as physically arrived at the branch.
     * Deliberately separate from confirm() below - "it showed up" and
     * "we checked it and it matches" are different moments in a real
     * receiving process, and a branch shouldn't have to claim the second
     * before they've actually had a chance to inspect the shipment.
     *
     * @throws OrderPlacementException
     */
    public function markDelivered(Staff $staff, RestockRequest $request): RestockRequest
    {
        $this->ensureCanTransitionTo($request, RestockRequestStatus::DELIVERED);

        $request->update([
            'status' => RestockRequestStatus::DELIVERED,
            'delivered_by_staff_id' => $staff->id,
            'delivered_at' => now(),
        ]);

        return $request->fresh();
    }

    /**
     * The only point where branch stock is actually credited. Takes an
     * explicit confirmed quantity rather than always trusting
     * requested_quantity, since a shipment can arrive short or damaged -
     * the branch is confirming what they actually got, not rubber-
     * stamping what was asked for. Defaults to the full requested amount
     * when not specified (the common case: everything arrived fine).
     *
     * @throws OrderPlacementException
     */
    public function confirm(Staff $staff, RestockRequest $request, ?int $confirmedQuantity = null): RestockRequest
    {
        $this->ensureCanTransitionTo($request, RestockRequestStatus::CONFIRMED);

        $confirmedQuantity ??= $request->requested_quantity;

        if ($confirmedQuantity < 0 || $confirmedQuantity > $request->requested_quantity) {
            throw new OrderPlacementException(
                "Confirmed quantity must be between 0 and {$request->requested_quantity} (what was requested/dispatched)."
            );
        }

        return DB::transaction(function () use ($staff, $request, $confirmedQuantity) {
            if ($confirmedQuantity > 0) {
                $branchStock = BranchStockLevel::query()
                    ->firstOrCreate(
                        ['branch_id' => $request->branch_id, 'book_id' => $request->book_id],
                        ['quantity' => 0, 'low_stock_threshold' => 5]
                    );
                $branchStock->increment('quantity', $confirmedQuantity);
            }

            $request->update([
                'status' => RestockRequestStatus::CONFIRMED,
                'confirmed_quantity' => $confirmedQuantity,
                'confirmed_by_staff_id' => $staff->id,
                'confirmed_at' => now(),
            ]);

            return $request->fresh();
        });
    }

    /**
     * Approves every still-pending item in a batch. Each item is
     * processed independently — one item hitting insufficient warehouse
     * stock does NOT block the rest of the batch from being approved,
     * since that's exactly the "approve some, reject/skip others" outcome
     * the review page is built to support. Returns both lists so the
     * controller can show a clear "3 approved, 1 failed: <reason>" summary
     * instead of a single pass/fail flash message.
     *
     * Scoped to PENDING items only, not APPROVED/DISPATCHED/etc — bulk
     * actions cover the initial review moment; later-stage transitions
     * (dispatch/deliver/confirm) are per-item only, since by that point
     * items in the same batch are commonly at different real-world
     * stages and a blanket "do this to everything" action stops making
     * sense.
     *
     * @return array{approved: Collection<int, RestockRequest>, failed: Collection<int, array{request: RestockRequest, error: string}>}
     */
    public function approveBatch(Staff $superadmin, string $batchId): array
    {
        $pending = RestockRequest::inBatch($batchId)->where('status', RestockRequestStatus::PENDING)->get();

        $approved = collect();
        $failed = collect();

        foreach ($pending as $request) {
            try {
                $approved->push($this->approve($superadmin, $request, notify: false));
            } catch (OrderPlacementException $e) {
                $failed->push(['request' => $request, 'error' => $e->getMessage()]);
            }
        }

        $this->notifyBatchOutcome($pending, $approved, $failed);

        return ['approved' => $approved, 'failed' => $failed];
    }

    /**
     * Rejects every still-pending item in a batch with one shared reason.
     * Unlike approve, rejection has no external failure mode (no
     * warehouse check), so this is a straightforward bulk update rather
     * than needing the same per-item try/catch as approveBatch().
     *
     * @return Collection<int, RestockRequest>
     */
    public function rejectBatch(Staff $superadmin, string $batchId, string $reason): Collection
    {
        $pending = RestockRequest::inBatch($batchId)->where('status', RestockRequestStatus::PENDING)->get();

        $rejected = $pending->map(fn ($request) => $this->reject($superadmin, $request, $reason, notify: false));

        $this->notifyBatchOutcome($pending, $rejected, collect());

        return $rejected;
    }

    /**
     * One consolidated notification to the requester per bulk action,
     * rather than the per-item RestockReviewedNotification that would
     * otherwise fire once per book - same "don't spam one email per line
     * item" reasoning as the batch-requested notification.
     */
    private function notifyBatchOutcome(Collection $pending, Collection $processed, Collection $failed): void
    {
        if ($pending->isEmpty()) {
            return;
        }

        $requester = $pending->first()->requestedBy;
        $requester?->notify(new RestockBatchReviewedNotification($processed, $failed));
    }

    /**
     * @throws OrderPlacementException
     */
    private function ensureCanTransitionTo(RestockRequest $request, RestockRequestStatus $target): void
    {
        if (! $request->status->canTransitionTo($target)) {
            throw new OrderPlacementException(
                "Cannot move this item from \"{$request->status->label()}\" to \"{$target->label()}\"."
            );
        }
    }
}
