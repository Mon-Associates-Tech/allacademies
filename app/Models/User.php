<?php

namespace App\Models;

use App\Traits\HasAvatar;
use App\Traits\Trackable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasAvatar;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'role_id',
        'is_online',
        'last_seen_at',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
    ];

    public function subscriptions(): User|HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    public function joinedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function ownedTeams(): User|HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function worksheets(): User|HasMany
    {
        return $this->hasMany(Worksheet::class);
    }

    /**
     * Primary role relationship (single role via role_id) - for backward compatibility
     */
    public function primaryRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Many-to-many roles relationship through role_user table
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the user's primary role name
     * First checks many-to-many roles, then falls back to single role, then string role
     */
    public function getRoleName(): ?string
    {
        // First try to get the first role from many-to-many relationship
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }

        if ($this->roles && $this->roles->isNotEmpty()) {
            return $this->roles->first()->name;
        }

        // Fall back to single role relationship if role_id is set
        if ($this->role_id) {
            if (!$this->relationLoaded('primaryRole')) {
                $this->load('primaryRole');
            }

            if ($this->primaryRole) {
                return $this->primaryRole->name;
            }
        }

        // Final fallback to string role field (use attributes to avoid accessor conflicts)
        return $this->attributes['role'] ?? null;
    }

    /**
     * Get all role names for this user
     */
    public function getRoleNames(): array
    {
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }

        $roleNames = $this->roles->pluck('name')->toArray();

        // Also include primary role if it exists and isn't already in the list
        $primaryRoleName = null;
        if ($this->role_id) {
            if (!$this->relationLoaded('primaryRole')) {
                $this->load('primaryRole');
            }

            if ($this->primaryRole) {
                $primaryRoleName = $this->primaryRole->name;
            }
        }

        // Add string role as fallback
        $stringRole = $this->attributes['role'] ?? null;

        // Combine all roles and remove duplicates
        $allRoles = array_filter(array_unique(array_merge(
            $roleNames,
            $primaryRoleName ? [$primaryRoleName] : [],
            $stringRole ? [$stringRole] : []
        )));

        return array_values($allRoles);
    }

    /**
     * Check if user has a specific role (checks all possible role sources)
     */
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoleNames());
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        $userRoles = $this->getRoleNames();
        return !empty(array_intersect($roleNames, $userRoles));
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->getRoleNames();
        return empty(array_diff($roleNames, $userRoles));
    }

    /**
     * Assign a role to the user via many-to-many relationship
     */
    public function assignRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return false;
        }

        if (!$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role->id);
        }

        return true;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return false;
        }

        $this->roles()->detach($role->id);
        return true;
    }

    /**
     * Sync user roles (replaces all current roles)
     */
    public function syncRoles(array $roleNames): bool
    {
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $this->roles()->sync($roleIds);
        return true;
    }

    public function student(): HasOne|User
    {
        return $this->hasOne(Student::class);
    }
}
