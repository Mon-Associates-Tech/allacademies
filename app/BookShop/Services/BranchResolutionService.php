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
}
