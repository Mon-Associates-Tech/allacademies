<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSchool
{
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    protected static function booted()
    {
        // Auto-scope queries to current school context
        static::addGlobalScope('school', function (Builder $builder) {
            //  $currentSchool = app('current_school');

            // Skip scoping for superadmin or when no school context
            //   if (!$currentSchool || (auth()->check() && auth()->user()->hasRole('superadmin'))) {
            //       return;
            //   }

            //  $builder->where($builder->getModel()->getTable() . '.school_id', $currentSchool->id);
        });

        // Auto-assign school_id when creating
        static::creating(function ($model) {
            if (! $model->school_id && app()->bound('current_school')) {
                $model->school_id = app('current_school')->id;
            }
        });
    }

    // Scope to query across schools (for superadmin use)
    public function scopeWithoutSchoolScope(Builder $query)
    {
        return $query->withoutGlobalScope('school');
    }

    public function scopeForSchool(Builder $query, $schoolId)
    {
        return $query->withoutGlobalScope('school')->where('school_id', $schoolId);
    }

    /**
     * Scope records to current user's school
     */
    public function scopeForCurrentUserSchool(Builder $query): Builder
    {
        $schoolId = auth()->user()?->school_id;

        return $schoolId ? $query->where('school_id', $schoolId) : $query->whereRaw('1 = 0');
    }

    /**
     * Boot the trait
     */
    protected static function bootBelongsToSchool(): void
    {
        // Automatically set school_id when creating records
        static::creating(function ($model) {
            if (! $model->school_id && auth()->user()?->school_id) {
                $model->school_id = auth()->user()->school_id;
            }
        });

        // Optional: Add global scope to automatically filter by user's school
        // Uncomment if you want automatic filtering (be careful with this)
        /*
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id && !app()->runningInConsole()) {
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
        */
    }

    /**
     * Check if model belongs to specific school
     */
    public function belongsToSchool($schoolId): bool
    {
        return $this->school_id == $schoolId;
    }

    /**
     * Check if model belongs to current user's school
     */
    public function belongsToCurrentUserSchool(): bool
    {
        return $this->school_id == auth()->user()?->school_id;
    }
}
