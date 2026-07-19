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
     * The branch a customer's region resolves to for order fulfillment.
     * Set/refreshed at order time via a resolution service (Phase 4) —
     * kept here as a cached convenience pointer, not the source of truth.
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
}
