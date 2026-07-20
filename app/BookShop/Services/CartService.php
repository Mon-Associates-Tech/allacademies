<?php

namespace App\BookShop\Services;

/**
 * Session-backed, not database-backed - a cart is inherently transient
 * pre-checkout state, and every other piece of BookShop already leans on
 * plain server-rendered forms rather than persisted client state, so this
 * stays consistent with that rather than introducing a new pattern.
 *
 * Deliberately single-branch: stock, and therefore what's orderable, is
 * branch-specific. Switching branches while the cart has items clears it
 * (see BranchSwitchController) rather than trying to merge or split into
 * multiple orders - simpler to reason about, and switching branches
 * mid-cart is an edge case, not the common path.
 */
class CartService
{
    private const SESSION_KEY = 'bookshop_cart';

    private function state(): array
    {
        return session(self::SESSION_KEY, ['branch_id' => null, 'items' => []]);
    }

    private function save(array $state): void
    {
        session([self::SESSION_KEY => $state]);
    }

    public function branchId(): ?int
    {
        return $this->state()['branch_id'];
    }

    /** @return array<int, int> book_id => quantity */
    public function items(): array
    {
        return $this->state()['items'];
    }

    public function setBranch(int $branchId): void
    {
        $state = $this->state();

        if ($state['branch_id'] !== $branchId) {
            $state = ['branch_id' => $branchId, 'items' => []];
        }

        $this->save($state);
    }

    public function add(int $bookId, int $quantity): void
    {
        $state = $this->state();
        $state['items'][$bookId] = ($state['items'][$bookId] ?? 0) + max(1, $quantity);
        $this->save($state);
    }

    /** @param  array<int, int>  $quantities  book_id => quantity, 0 or less removes the line */
    public function updateQuantities(array $quantities): void
    {
        $state = $this->state();
        $state['items'] = array_filter($quantities, fn ($q) => (int) $q > 0);
        $this->save($state);
    }

    public function remove(int $bookId): void
    {
        $state = $this->state();
        unset($state['items'][$bookId]);
        $this->save($state);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function isEmpty(): bool
    {
        return empty($this->items());
    }
}
