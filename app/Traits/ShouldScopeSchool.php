<?php

namespace App\Traits;

trait ShouldScopeSchool
{
    // In models that should be school-scoped (like Teacher, Student, etc.)
    public function scopeForSchool($query, $schoolId = null)
    {
        if (! $schoolId) {
            $schoolId = auth()->user()->school_id ?? null;
        }

        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }

        return $query->whereRaw('1=0'); // No results if no school context
    }

    public function scopeForCurrentSchool($query)
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Super admins and owners might see all or specific schools
            if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                // If current_school_id is bound, use it to filter
                if (app()->bound('current_school_id')) {
                    $schoolId = app('current_school_id');
                    if ($schoolId > 0) {
                        return $query->where('school_id', $schoolId);
                    }

                    // If $schoolId is null or 0, return all schools (no filter)
                    return $query;
                }

                // If not bound yet, return all schools (no filter)
                return $query;
            }

            // Regular users see only their school
            return $query->where('school_id', $user->school_id);
        }

        return $query->whereRaw('1=0'); // No results for unauthenticated users
    }
}
