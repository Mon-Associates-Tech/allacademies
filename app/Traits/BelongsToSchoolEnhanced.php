<?php

namespace App\Traits;

use App\Scopes\SchoolScope;
use Illuminate\Support\Facades\Auth;
use Schema;

trait BelongsToSchoolEnhanced
{
    public static function bootBelongsToSchoolEnhanced(): void
    {
        // Check if this model should be school-scoped
        if (static::shouldApplySchoolScope()) {
            static::addGlobalScope(new SchoolScope);
        }

        // Auto-assign school_id when creating (only for school-restricted models)
        static::creating(function ($model) {
            // Skip auto-assignment if model shouldn't be school-restricted
            if (!$model->shouldAutoAssignSchool()) {
                return;
            }

            // Check if school_id column exists on this model's table
            static $columnExists = null;
            $table = $model->getTable();

            if ($columnExists === null) {
                $columnExists = Schema::hasColumn($table, 'school_id');
            }

            // Only proceed if school_id column exists and is empty
            if ($columnExists && empty($model->school_id)) {
                // Try to get school_id from authenticated user
                $schoolId = self::getSchoolIdFromContext();

                if ($schoolId) {
                    $model->school_id = $schoolId;
                }
            }
        });
    }

    /**
     * Determine if this model should have school scope applied
     * This is the core logic that handles all user roles and model types
     */
    protected static function shouldApplySchoolScope(): bool
    {
        // Models that should NEVER be school-scoped
        $globalModels = [
            \App\Models\School::class,
            \App\Models\User::class, // Users are never scoped - they provide context
            // Add other global resources
            // \App\Models\Category::class,
            // \App\Models\SystemSetting::class,
        ];

        if (in_array(static::class, $globalModels)) {
            return false;
        }

        // Check if model explicitly disables school scoping
        if (property_exists(static::class, 'schoolRestricted') && !static::$schoolRestricted) {
            return false;
        }

        // Check authenticated user context
        $user = auth()->user();

        if (!$user) {
            return true; // Default to scoped if no user context
        }

        // Roles that can access across all schools (never scoped)
        $crossSchoolRoles = ['superadmin', 'owner', 'moderator'];

        if ($user->hasAnyRole($crossSchoolRoles)) {
            return false; // No scoping for these roles
        }

        // Special handling for Author model - not school-bound by design
        if (static::class === \App\Models\Author::class) {
            return false; // Authors are global
        }

        // Roles that are not school-bound (no associated school context)
        $nonSchoolRoles = ['subscriber', 'moderator'];

        // If user only has non-school roles, don't scope
        if ($user->hasAnyRole($nonSchoolRoles)) {
            return false;
        }

        // Default: apply school scope for all other cases
        return true;
    }

    /**
     * Determine if this model should auto-assign school_id when creating
     */
    protected function shouldAutoAssignSchool(): bool
    {
        // Models that should never auto-assign school
        $excludedModels = [
            \App\Models\School::class,
            \App\Models\User::class,
            \App\Models\Author::class, // Authors are global
        ];

        if (in_array(static::class, $excludedModels)) {
            return false;
        }

        // Check if model has a property to disable auto-assignment
        if (property_exists($this, 'autoAssignSchool') && !$this->autoAssignSchool) {
            return false;
        }

        // Check user context
        $user = auth()->user();

        if (!$user) {
            return false; // Can't assign without user context
        }

        // Don't auto-assign for non-school roles
        $nonSchoolRoles = ['subscriber', 'moderator'];
        if ($user->hasAnyRole($nonSchoolRoles)) {
            return false;
        }

        // Default: auto-assign school for school-bound models
        return true;
    }

    protected static function getSchoolIdFromContext()
    {
        try {
            // Check if we're in a web request with authenticated user
            if (app()->bound('auth') && app('auth')->hasResolvedGuards()) {
                $user = Auth::user();

                if ($user) {
                    // Cross-school roles don't auto-assign school
                    $crossSchoolRoles = ['superadmin', 'owner', 'moderator'];
                    if ($user->hasAnyRole($crossSchoolRoles)) {
                        // Check if there's a current school context for these users
                        if (app()->bound('current_school')) {
                            return app('current_school')->id;
                        }
                        return null;
                    }

                    // Non-school roles don't have school context
                    $nonSchoolRoles = ['subscriber', 'moderator'];
                    if ($user->hasAnyRole($nonSchoolRoles)) {
                        return null;
                    }

                    // Regular school users use their school_id
                    return $user->school_id;
                }
            }

            // Fallback to current_school if available
            if (app()->bound('current_school')) {
                return app('current_school')->id;
            }
        } catch (\Exception $e) {
            // If anything fails, return null to avoid breaking the application
            return null;
        }

        return null;
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeWithoutSchoolScope($query)
    {
        return $query->withoutGlobalScope(SchoolScope::class);
    }

    public function scopeCrossSchool($query)
    {
        $user = auth()->user();

        if (!$user || (!$user->hasAnyRole(['superadmin', 'owner', 'moderator']))) {
            abort(403, 'Unauthorized to access cross-school data');
        }

        return $query->withoutGlobalScope(SchoolScope::class);
    }
}
