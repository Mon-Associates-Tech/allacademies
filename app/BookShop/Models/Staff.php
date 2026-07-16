<?php

namespace App\BookShop\Models;

use App\BookShop\Enums\StaffRole;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * Standalone identity for the BookShop module. Intentionally does NOT
 * extend App\Models\User or touch the platform `users` table — this
 * lets the module be authenticated (guard: bookshop_staff) and
 * extracted independently of the host application.
 */
class Staff extends Model implements AuthenticatableContract, MustVerifyEmail
{
    use Authenticatable, Authorizable, HasFactory, Notifiable;

    protected $table = 'bookshop_staff';

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => StaffRole::class,
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === StaffRole::SUPERADMIN;
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === StaffRole::ADMIN;
    }
}
