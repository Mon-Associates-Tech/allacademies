<?php

namespace App\BookShop\Services;

use App\BookShop\Models\Branch;
use App\BookShop\Models\Customer;

/**
 * Resolves which branch should fulfill a customer's order, based on the
 * customer's stored region/city (from the same <x-location-selector>
 * fields used on Branch). Deliberately conservative: returns null rather
 * than guessing when nothing matches, so OrderPlacementService can block
 * the order with a clear message instead of silently routing it to the
 * wrong branch.
 */
class BranchResolutionService
{
    public function resolveForCustomer(Customer $customer): ?Branch
    {
        if (! $customer->region) {
            return null;
        }

        // 1. Exact region + city match.
        if ($customer->city) {
            $branch = Branch::query()
                ->active()
                ->where('region', $customer->region)
                ->where('city', $customer->city)
                ->first();

            if ($branch) {
                return $branch;
            }
        }

        // 2. Same region, any city - the closest available fallback without
        //    a geocoding/distance service in place.
        return Branch::query()
            ->active()
            ->where('region', $customer->region)
            ->first();
    }

    /**
     * The branch a shopper is currently browsing/ordering at - their
     * explicit choice (persisted in both the cart session and, for a
     * logged-in customer, preferred_branch_id) if they've picked one and
     * it's still active, otherwise the region-based default from
     * resolveForCustomer(). Used by CatalogController/BookShowController/
     * CartController (with CartService) and BulkOrderController (with
     * BulkOrderCartService) - accepts either via ShoppingCartContract
     * rather than being hard-typed to one, since branch resolution works
     * identically for both; resolveForCustomer() stays reserved for the
     * initial/default case.
     *
     * $customer is nullable to support guest browsing (regular
     * catalog/cart only - bulk ordering always requires an account) - a
     * guest has no region on file, so they only ever get a branch once
     * they've explicitly picked one via BranchSwitchController. A
     * logged-in customer without an explicit choice yet still gets the
     * region-based default, same as before.
     */
    public function resolveCurrentShoppingBranch(?Customer $customer, ShoppingCartContract $cart): ?Branch
    {
        if ($branchId = $cart->branchId()) {
            $branch = Branch::query()->active()->find($branchId);

            if ($branch) {
                return $branch;
            }
        }

        if (! $customer) {
            return null;
        }

        $branch = $customer->preferred_branch_id
            ? Branch::query()->active()->find($customer->preferred_branch_id)
            : null;

        $branch ??= $this->resolveForCustomer($customer);

        if ($branch) {
            $cart->setBranch($branch->id);
        }

        return $branch;
    }
}
