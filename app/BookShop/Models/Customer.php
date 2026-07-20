<?php

namespace App\BookShop\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * Standalone identity for BookShop customers (guard: bookshop_customer).
 * Independent of App\Models\User / School — a customer is anyone who
 * registers to order physical books, regardless of platform account.
 *
 * country/country_code/region/city map directly onto the existing
 * <x-location-selector> / country-select / region-select / city-select
 * components already used elsewhere in the project.
 */
class Customer extends Model implements AuthenticatableContract, MustVerifyEmail
{
    use Authenticatable, Authorizable, HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $table = 'bookshop_customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'country',
        'country_code',
        'region',
        'city',
        'address',
        'is_active',
        'preferred_branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The branch a customer is currently shopping at. Set by
     * BranchSwitchController when they explicitly switch, and used as a
     * fallback by BranchResolutionService::resolveCurrentShoppingBranch()
     * ahead of the region-based default - no longer just a cached
     * convenience pointer now that cross-branch shopping is a real flow.
     */
    public function preferredBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'preferred_branch_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * See Staff::notifications() for why this override exists - same
     * reasoning, points at bookshop_notifications instead of the
     * framework default table.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    /**
     * Single source of truth for which customers a staff member can see,
     * matching the Branch/Order/RestockRequest scopeVisibleTo(Staff)
     * pattern:
     *  - superadmin: unrestricted, sees every customer
     *  - admin: customers whose current shopping branch is theirs, OR
     *    who have placed at least one order there - catches customers
     *    who ordered cross-branch even if their preferred branch is
     *    elsewhere.
     */
    public function scopeVisibleTo($query, Staff $staff)
    {
        if ($staff->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($staff) {
            $q->where('preferred_branch_id', $staff->branch_id)
                ->orWhereHas('orders', fn ($oq) => $oq->where('branch_id', $staff->branch_id));
        });
    }
}
