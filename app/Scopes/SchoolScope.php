<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scoping for User model to avoid circular references
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Check if we have school context from middleware
        if (app()->bound('current_school_id')) {
            $schoolId = app('current_school_id');

            // If null, they want to see all schools (no scoping)
            if ($schoolId === null) {
                return;
            }

            // If it's a valid school ID, apply scoping
            if ($schoolId > 0) {
                $builder->where($model->getTable() . '.school_id', $schoolId);
                return;
            }
        }

        // Fallback to user context
        if (Auth::hasUser() && Auth::check()) {
            $user = Auth::user();

            // Skip scoping for super admins and owners by default
            if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                return;
            }

            // Apply school scoping for regular users
            if ($user->school_id) {
                $builder->where($model->getTable() . '.school_id', $user->school_id);
            }
        }
    }
}
