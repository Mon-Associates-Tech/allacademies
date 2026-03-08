<?php

namespace App\Traits;

use App\Models\School;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToSchoolEnhanced
{
    public function getUserSchoolId(): ?int
    {
        if (!empty($this->attributes['school_id'])) {
            return $this->attributes['school_id'];
        }

        if (isset($this->user) && $this->user->school_id) {
            return $this->user->school_id;
        }

        return getSchoolId();
    }

    public function getSchoolForUser(): ?School
    {
        $schoolId = $this->getUserSchoolId();
        return $schoolId ? School::find($schoolId) : null;
    }

    public static function bootBelongsToSchoolEnhanced(): void
    {
        // Apply global school scope if needed
        if (static::shouldApplySchoolScope()) {
            static::addGlobalScope(new SchoolScope);
        }

        // Auto-assign school_id when creating
        static::creating(function ($model) {
            if (!$model->shouldAutoAssignSchool()) {
                return;
            }

            // Check if school_id column exists
            static $columnExists = null;
            if ($columnExists === null) {
                $columnExists = Schema::hasColumn($model->getTable(), 'school_id');
            }

            // Only auto-assign if column exists and is empty
            if ($columnExists && empty($model->school_id)) {
                $schoolId = static::getSchoolIdFromContext();
                if ($schoolId) {
                    $model->school_id = $schoolId;
                }
            }
        });
    }

    /**
     * Determine if this model should have school scope applied
     */
    protected static function shouldApplySchoolScope(): bool
    {
        // Models that should NEVER be school-scoped
        $globalModels = [
            \App\Models\School::class,
            \App\Models\User::class, // Users provide context, not scoped by it
        ];

        if (in_array(static::class, $globalModels)) {
            return false;
        }

        // Don't apply if model doesn't have school_id column
        if (!Schema::hasColumn((new static)->getTable(), 'school_id')) {
            return false;
        }

        // Check if model explicitly disables scoping via property
        if (property_exists(static::class, 'schoolRestricted') && !static::$schoolRestricted) {
            return false;
        }

        // Special handling for Author model
        if (static::class === \App\Models\Author::class) {
            return false; // Authors are global
        }

        // Check authenticated user context
        $user = auth()->user();

        if (!$user) {
            // No user = default to scoped (safe)
            return true;
        }

        // Cross-school roles: only scope if they've selected a specific school
        if ($user->isSuperAdmin() || $user->hasRole('owner')) {
            // If current_school_id is bound and NOT null, apply scope
            if (app()->bound('current_school_id')) {
                $schoolId = app('current_school_id');
                // null = "all schools" mode = no scope
                // integer = specific school = apply scope
                return $schoolId !== null;
            }
            // Not bound = default to "all schools" for owners
            return false;
        }

        // Default: apply school scope for regular users
        return true;
    }

    /**
     * Determine if this model should auto-assign school_id when creating
     */
    protected function shouldAutoAssignSchool(): bool
    {
        // Models that should never auto-assign
        $excludedModels = [
            \App\Models\School::class,
            \App\Models\User::class,
            \App\Models\Author::class, // Authors are global
        ];

        if (in_array(static::class, $excludedModels)) {
            return false;
        }

        // Check for explicit opt-out property
        if (property_exists($this, 'autoAssignSchool') && !$this->autoAssignSchool) {
            return false;
        }

        $user = auth()->user();

        if (!$user) {
            return false; // Can't auto-assign without user
        }

        // Non-school roles don't auto-assign
        // Don't auto-assign for non-school roles
        $nonSchoolRoles = ['guest'];
        if ($user->hasAnyRole($nonSchoolRoles)) {
            return false;
        }

        return true;
    }

    /**
     * Get school ID from current context
     */
    protected static function getSchoolIdFromContext(): ?int
    {
        try {
            // Priority 1: Middleware-set context
            if (app()->bound('current_school_id')) {
                $schoolId = app('current_school_id');
                // null = "all schools" (valid for owners)
                // Return as-is (could be null or int)
                return $schoolId;
            }

            // Priority 2: Authenticated user context
            if (app()->bound('auth') && app('auth')->hasResolvedGuards()) {
                $user = Auth::user();

                if ($user) {
                    // Cross-school roles
                    if ($user->isSuperAdmin() || $user->hasRole('owner')) {
                        // Check for current_school binding
                        if (app()->bound('current_school')) {
                            return app('current_school')->id;
                        }
                        // No context = null (don't auto-assign for owners)
                        return null;
                    }

                    // Non-school roles don't have school context
                    $nonSchoolRoles = ['guest'];
                    if ($user->hasAnyRole($nonSchoolRoles)) {
                        return null;
                    }

                    // Regular school-bound users
                    return $user->school_id;
                }
            }

            // Priority 3: Fallback to current_school
            if (app()->bound('current_school')) {
                return app('current_school')->id;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get school context', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }

        return null;
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope to a specific school (bypasses global scope)
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->withoutGlobalScope(SchoolScope::class)
            ->where('school_id', $schoolId);
    }

    /**
     * Remove school scope (for internal queries)
     */
    public function scopeWithoutSchoolScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SchoolScope::class);
    }

    /**
     * Query all schools (requires permission)
     */
    public function scopeCrossSchool(Builder $query): Builder
    {
        $user = auth()->user();

        if (! $user || (! $user->hasAnyRole(['superadmin', 'owner']))) {
            abort(403, 'Unauthorized to access cross-school data');
        }

        return $query->withoutGlobalScope(SchoolScope::class);
    }

    /**
     * Explicit "all schools" scope with permission check
     */
    public function scopeAllSchools(Builder $query): Builder
    {
        if (!auth()->check()) {
            abort(403, 'Authentication required');
        }

        $user = auth()->user();
        if (!($user->isSuperAdmin() || $user->hasRole('owner'))) {
            abort(403, 'Unauthorized to access all schools');
        }

        return $query->withoutGlobalScope(SchoolScope::class);
    }

    /**
     * Scope to current school context (mostly redundant now)
     * Kept for backwards compatibility
     */
    public function scopeForCurrentSchool(Builder $query): Builder
    {
        // Global scope handles this automatically
        // But we keep it for explicit calls
        return $query;
    }

    // ==================== RELATIONSHIP ====================

    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
