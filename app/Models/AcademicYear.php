<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Ensure only one current year per school
        static::saving(function ($year) {
            if ($year->is_current) {
                static::where('school_id', $year->school_id)
                    ->where('id', '!=', $year->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    // Helper methods
    public function getDisplayName(): string
    {
        return $this->name ?: $this->generateName();
    }

    public function generateName(): string
    {
        $startYear = $this->start_date->year;
        $endYear = $this->end_date->year;

        if ($startYear === $endYear) {
            return (string) $startYear;
        }

        return "{$startYear}/{$endYear}";
    }

    public function isActive(): bool
    {
        $now = Carbon::now();

        return $this->status === 'active' &&
            $now->between($this->start_date, $this->end_date);
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getProgressPercentage(): float
    {
        $now = Carbon::now();

        if ($now->lt($this->start_date)) {
            return 0;
        }

        if ($now->gt($this->end_date)) {
            return 100;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $passedDays = $this->start_date->diffInDays($now);

        return ($passedDays / $totalDays) * 100;
    }

    public static function getCurrentForSchool($schoolId): ?AcademicYear
    {
        return static::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();
    }
}
