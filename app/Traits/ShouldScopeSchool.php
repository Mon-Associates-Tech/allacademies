<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ShouldScopeSchool
{
    /**
     * Boot the trait - adds AUTOMATIC global scope
     */
    protected static function bootShouldScopeSchool(): void
    {
        // Add global scope that applies to ALL queries automatically
        static::addGlobalScope('school_scoped', function (Builder $builder) {
            if (!auth()->check()) {
                // No user = no results (safe default)
                $builder->whereRaw('1 = 0');
                return;
            }

            $user = auth()->user();

            // Cross-school users (owner/superadmin)
            if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                // Check what context they're in
                if (app()->bound('current_school_id')) {
                    $schoolId = app('current_school_id');

                    // null = "all schools" mode - no filter
                    if ($schoolId === null) {
                        return;
                    }

                    // Valid school selected - filter to that school
                    if ($schoolId > 0) {
                        $builder->where($builder->getModel()->getTable() . '.school_id', $schoolId);
                        return;
                    }
                }

                // Fallback: if no context set, default to "all schools" for owners
                return;
            }

            // School-bound users - MUST have school_id
            if (!$user->school_id) {
                // User has no school = no access (security)
                $builder->whereRaw('1 = 0');
                return;
            }

            // Regular users: locked to their school
            $builder->where($builder->getModel()->getTable() . '.school_id', $user->school_id);
        });

        // Auto-assign school_id on creation
        static::creating(function ($model) {
            if ($model->school_id) {
                return; // Already set, don't override
            }

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            // Cross-school users: use current context
            if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                if (app()->bound('current_school_id')) {
                    $schoolId = app('current_school_id');
                    if ($schoolId > 0) {
                        $model->school_id = $schoolId;
                    }
                }
                return;
            }

            // School-bound users: use their school
            if ($user->school_id) {
                $model->school_id = $user->school_id;
            }
        });
    }

    /**
     * Explicit scope: query a specific school (bypass global scope)
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->withoutGlobalScope('school_scoped')
            ->where('school_id', $schoolId);
    }

    /**
     * Explicit scope: query all schools (requires permission)
     */
    public function scopeAllSchools(Builder $query): Builder
    {
        if (!auth()->check()) {
            abort(403, 'Authentication required');
        }

        $user = auth()->user();
        if (!($user->isSuperAdmin() || $user->hasRole('owner'))) {
            abort(403, 'Unauthorized to access cross-school data');
        }

        return $query->withoutGlobalScope('school_scoped');
    }

    /**
     * Legacy method - kept for backwards compatibility
     * But now it just calls the global scope logic
     */
    public function scopeForCurrentSchool(Builder $query): Builder
    {
        // The global scope already handles this, but we keep the method
        // in case it's explicitly called somewhere
        return $query;
    }
}
