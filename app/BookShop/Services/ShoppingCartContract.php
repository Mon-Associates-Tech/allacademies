<?php

namespace App\BookShop\Services;

/**
 * Implemented by both CartService and BulkOrderCartService so
 * BranchResolutionService::resolveCurrentShoppingBranch() can accept
 * either without being hard-typed to one - the two builders are
 * deliberately separate classes (different lifecycle, different session
 * key), but branch resolution works identically for both.
 */
interface ShoppingCartContract
{
    public function branchId(): ?int;

    public function setBranch(int $branchId): void;
}
