<?php

namespace App\Policies;

use App\Models\User;

class PayrollPolicy
{
    public function viewPayroll(User $user): bool
    {
        return in_array($user->role?->value ?? $user->role, ['admin', 'accountant']);
    }

    public function managePayroll(User $user): bool
    {
        return in_array($user->role?->value ?? $user->role, ['admin', 'accountant']);
    }

    public function approvePayroll(User $user): bool
    {
        return ($user->role?->value ?? $user->role) === 'admin';
    }

    public function managePayrollRoles(User $user): bool
    {
        return in_array($user->role?->value ?? $user->role, ['admin', 'accountant']);
    }
}
