<?php

namespace App\BookShop\Enums;

/**
 * Roles within the BookShop module's own auth system.
 *
 * Deliberately just two roles (per project scope):
 *  - SUPERADMIN: unrestricted, sees/manages all branches
 *  - ADMIN: branch-scoped, sees/manages only their assigned branch
 */
enum StaffRole: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Admin',
            self::ADMIN => 'Branch Admin',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPERADMIN;
    }
}
