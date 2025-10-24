<?php

namespace App\Models;

use App\Models\Attendance\Attendance;
use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AcademicPeriod extends Model
{
    use HasFactory, BelongsToSchoolEnhanced;

    protected $fillable = [
        'school_id',
        'title',
        'type',
        'sequence',
        'start_date',
        'end_date',
        'status',
        'settings',
        'academic_year',
        'year_sequence',
        'description',
        'total_weeks',
        'registration_start',
        'registration_end',
        'exam_start',
        'exam_end',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'exam_start' => 'date',
        'exam_end' => 'date',
        'settings' => 'array',
        'is_current' => 'boolean',
        'total_weeks' => 'integer',
        'sequence' => 'integer',
        'year_sequence' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Ensure only one current period per school
        static::saving(function ($period) {
            if ($period->is_current) {
                // Set all other periods for this school to not current
                static::where('school_id', $period->school_id)
                    ->where('id', '!=', $period->id)
                    ->update(['is_current' => false]);
            }
        });

        // Auto-calculate total weeks if not provided
        static::creating(function ($period) {
            if (!$period->total_weeks && $period->start_date && $period->end_date) {
                $period->total_weeks = $period->start_date->diffInWeeks($period->end_date);
            }

            // Auto-generate academic year if not provided
            if (!$period->academic_year && $period->start_date) {
                $period->academic_year = $period->generateAcademicYear();
            }
        });

        static::updating(function ($period) {
            if ($period->isDirty(['start_date', 'end_date']) && !$period->isDirty('total_weeks')) {
                $period->total_weeks = $period->start_date->diffInWeeks($period->end_date);
            }
        });
    }

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
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

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeCurrentOrUpcoming($query)
    {
        return $query->whereIn('status', ['active', 'upcoming']);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function generateAcademicYear(): string
    {
        $startYear = $this->start_date->year;
        $endYear = $this->end_date->year;

        if ($startYear === $endYear) {
            return (string) $startYear;
        }

        return "{$startYear}/{$endYear}";
    }

    public function isCurrentPeriod(): bool
    {
        return $this->is_current;
    }

    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' &&
            $now->between($this->start_date, $this->end_date);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' &&
            Carbon::now()->lt($this->start_date);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' ||
            Carbon::now()->gt($this->end_date);
    }

    public function getDurationInWeeks(): int
    {
        return $this->start_date->diffInWeeks($this->end_date);
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

    public function getRemainingDays(): int
    {
        $now = Carbon::now();

        if ($now->gt($this->end_date)) {
            return 0;
        }

        return $now->diffInDays($this->end_date);
    }

    public function getDisplayName(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $typeTitle = ucfirst($this->type);
        return "{$typeTitle} {$this->sequence}";
    }

    public function canRegister(): bool
    {
        if (!$this->registration_start || !$this->registration_end) {
            return $this->status === 'upcoming';
        }

        $now = Carbon::now();
        return $now->between($this->registration_start, $this->registration_end);
    }

    public function isExamPeriod(): bool
    {
        if (!$this->exam_start || !$this->exam_end) {
            return false;
        }

        $now = Carbon::now();
        return $now->between($this->exam_start, $this->exam_end);
    }

    // Static helper methods
    public static function getCurrentPeriodForSchool($schoolId): ?AcademicPeriod
    {
        return static::where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();
    }

    public static function getPeriodsForAcademicYear($schoolId, $academicYear)
    {
        return static::where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->orderBy('year_sequence')
            ->orderBy('sequence')
            ->get();
    }

    public static function createPeriodForSchool($schoolId, array $data): AcademicPeriod
    {
        $data['school_id'] = $schoolId;
        return static::create($data);
    }

    // Validation rules
    public static function validationRules(): array
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:semester,term,quarter,trimester,session',
            'sequence' => 'required|integer|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'is_current' => 'boolean',
            'academic_year' => 'nullable|string|max:20',
            'year_sequence' => 'nullable|integer|min:1|max:10',
            'description' => 'nullable|string|max:1000',
            'total_weeks' => 'nullable|integer|min:1|max:52',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after_or_equal:registration_start',
            'exam_start' => 'nullable|date|after_or_equal:start_date',
            'exam_end' => 'nullable|date|after_or_equal:exam_start|before_or_equal:end_date',
        ];
    }
}
