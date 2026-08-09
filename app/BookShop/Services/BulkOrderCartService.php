<?php

namespace App\BookShop\Services;

/**
 * Sibling to CartService, not a reuse of it - a bulk order request is a
 * fundamentally different lifecycle (submit -> staff reviews & quotes ->
 * customer accepts -> becomes a real Order) rather than immediate
 * purchase, and deliberately uses its own session key so building a
 * bulk request doesn't touch or get touched by whatever's sitting in
 * the customer's normal shopping cart at the same time.
 */
class BulkOrderCartService implements ShoppingCartContract
{
    private const SESSION_KEY = 'bookshop_bulk_order_cart';

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

    public function totalQuantity(): int
    {
        return array_sum($this->items());
    }

    public function isEmpty(): bool
    {
        return empty($this->items());
    }
}
