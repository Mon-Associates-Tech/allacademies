<?php

namespace App\BookShop\Enums;

/**
 * Full warehouse-to-branch lifecycle, not just approve/reject:
 *   PENDING (in review) -> APPROVED -> DISPATCHED -> DELIVERED -> CONFIRMED
 *                       \-> REJECTED
 * Stock only actually moves at two points: warehouse stock is debited
 * (reserved) on APPROVE, and branch stock is credited on CONFIRM - not
 * on approval like the old two-status version. This models a real
 * receiving process: goods aren't "in branch inventory" until someone at
 * the branch has physically checked them in, which is also why
 * CONFIRMED carries its own confirmed_quantity separate from what was
 * originally requested (a shipment can arrive short or damaged).
 */
enum RestockRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DISPATCHED = 'dispatched';
    case DELIVERED = 'delivered';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'In Review',
            self::APPROVED => 'Approved',
            self::DISPATCHED => 'Dispatched',
            self::DELIVERED => 'Delivered',
            self::CONFIRMED => 'Confirmed',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Single source of truth for valid transitions, same pattern as
     * OrderStatus::allowedNextStatuses(). Rejection stays reachable from
     * APPROVED (not just PENDING) - an approved-but-not-yet-shipped item
     * needs an escape valve too, since approving already reserves
     * warehouse stock; rejecting an APPROVED item releases that
     * reservation back to the warehouse. Once DISPATCHED, the physical
     * shipment is already in motion, so rejection is no longer offered -
     * the pipeline just runs forward from there.
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::PENDING => [self::APPROVED, self::REJECTED],
            self::APPROVED => [self::DISPATCHED, self::REJECTED],
            self::DISPATCHED => [self::DELIVERED],
            self::DELIVERED => [self::CONFIRMED],
            self::CONFIRMED, self::REJECTED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedNextStatuses(), true);
    }

    public function isTerminal(): bool
    {
        return $this === self::CONFIRMED || $this === self::REJECTED;
    }

    /**
     * True once warehouse stock has been debited for this request (on
     * approval) but before it's been released again - used to decide
     * whether rejecting/cancelling needs to credit the warehouse back.
     */
    public function hasReservedWarehouseStock(): bool
    {
        return in_array($this, [self::APPROVED, self::DISPATCHED, self::DELIVERED], true);
    }
}
