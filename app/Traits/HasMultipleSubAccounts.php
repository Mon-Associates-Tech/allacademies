<?php

namespace App\Traits;

use App\Models\Subaccount;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMultipleSubAccounts
{
    /**
     * Get all subaccounts for this model (polymorphic one-to-many)
     */
    public function subaccounts(): MorphMany
    {
        return $this->morphMany(Subaccount::class, 'subaccountable');
    }

    /**
     * Get the primary subaccount (polymorphic one-to-one for primary)
     * Backward compatibility method
     */
    public function subaccount(): MorphOne
    {
        return $this->morphOne(Subaccount::class, 'subaccountable')
            ->where('is_primary', true);
    }

    /**
     * Get all active subaccounts
     */
    public function activeSubaccounts()
    {
        return $this->subaccounts()
            ->where('status', 'active')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get primary active subaccount
     */
    public function primarySubaccount(): ?Subaccount
    {
        return $this->subaccounts()
            ->where('is_primary', true)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get all secondary subaccounts
     */
    public function secondarySubaccounts()
    {
        return $this->subaccounts()
            ->where('is_primary', false)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Set a subaccount as primary
     */
    public function setPrimarySubaccount(Subaccount $subaccount): bool
    {
        // Ensure the subaccount belongs to this model
        if ($subaccount->subaccountable_id !== $this->id ||
            $subaccount->subaccountable_type !== get_class($this)) {
            return false;
        }

        // Remove primary flag from other subaccounts
        $this->subaccounts()
            ->where('id', '!=', $subaccount->id)
            ->update(['is_primary' => false]);

        // Set this one as primary
        return $subaccount->update(['is_primary' => true, 'status' => 'active']);
    }

    /**
     * Check if model has any subaccounts
     */
    public function hasSubaccounts(): bool
    {
        return $this->subaccounts()->exists();
    }

    /**
     * Check if model has a primary subaccount
     */
    public function hasPrimarySubaccount(): bool
    {
        return $this->subaccounts()
            ->where('is_primary', true)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if model has multiple active subaccounts
     */
    public function hasMultipleActiveSubaccounts(): bool
    {
        return $this->subaccounts()
            ->where('status', 'active')
            ->count() > 1;
    }

    /**
     * Get subaccount by code
     */
    public function getSubaccountByCode(string $code): ?Subaccount
    {
        return $this->subaccounts()
            ->where('subaccount_code', $code)
            ->first();
    }

    /**
     * Get subaccount by name
     */
    public function getSubaccountByName(string $name): ?Subaccount
    {
        return $this->subaccounts()
            ->where('name', $name)
            ->first();
    }

    /**
     * Deactivate a subaccount
     */
    public function deactivateSubaccount(Subaccount $subaccount): bool
    {
        if ($subaccount->subaccountable_id !== $this->id ||
            $subaccount->subaccountable_type !== get_class($this)) {
            return false;
        }

        // If this was primary, set another as primary
        if ($subaccount->is_primary) {
            $newPrimary = $this->subaccounts()
                ->where('id', '!=', $subaccount->id)
                ->where('status', 'active')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        return $subaccount->update(['status' => 'inactive']);
    }

    /**
     * Activate a subaccount
     */
    public function activateSubaccount(Subaccount $subaccount): bool
    {
        if ($subaccount->subaccountable_id !== $this->id ||
            $subaccount->subaccountable_type !== get_class($this)) {
            return false;
        }

        return $subaccount->update(['status' => 'active']);
    }

    /**
     * Get count of active subaccounts
     */
    public function activeSubaccountsCount(): int
    {
        return $this->subaccounts()
            ->where('status', 'active')
            ->count();
    }
}
