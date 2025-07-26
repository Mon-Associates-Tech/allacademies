<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
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
        return $this->belongsToMany(Role::class, 'role_user');
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

        // Also include a primary role if it exists and isn't already in the list
        $primaryRoleName = null;
        if ($this->role_id) {
            if (!$this->relationLoaded('primaryRole')) {
                $this->load('primaryRole');
            }

            if ($this->primaryRole) {
                $primaryRoleName = $this->primaryRole->name;
            }
        }

        // Add string role as fallback, handling enum value
        $stringRole = null;
        if (isset($this->attributes['role'])) {
            $stringRole = $this->attributes['role'] instanceof UserRole
                ? $this->attributes['role']->value
                : $this->attributes['role'];
        }

        $allRoles = array_filter(array_unique(array_merge(
            $roleNames,
            $primaryRoleName ? [$primaryRoleName] : [],
            $stringRole ? [$stringRole] : $stringRole
        )));

        return array_values($allRoles);
    }

    /**
     * Check if user has a specific role (checks all possible role sources)
     */
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoleNames(), true);
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
     * Check if a user has all the given roles
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

}
