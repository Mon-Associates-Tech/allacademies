<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        // Basic information
        'name',
        'code',
        'email',
        'phone',
        'website',
        'logo',
        'letterhead_template',

        // Location
        'address',
        'city',
        'state',
        'country',
        'postal_code',

        // School details
        'type',
        'description',
        'student_capacity',

        // System settings
        'status',
        'subscription_plan',
        'subscription_ends_at',
        'settings',
        'timezone',
        'currency',

        // Academic year
        'academic_year_start',
        'academic_year_end',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_ends_at' => 'datetime',
        'established_date' => 'date'
    ];

    // Relationships


    /**
     * Get the school's subaccount (polymorphic)
     */
    public function subaccount(): MorphOne
    {
        return $this->morphOne(Subaccount::class, 'subaccountable');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class);
    }

    public function getSetting($key, $default = null)
    {
        return SchoolSetting::getForSchool($this->id, $key, $default);
    }

    public function setSetting($key, $value)
    {
        return SchoolSetting::setForSchool($this->id, $key, $value);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            if (empty($school->code)) {
                $school->code = $school->generateSchoolCode();
            }
        });
    }

    public function generateSchoolCode(): string
    {
        $prefix = strtoupper(substr($this->name, 0, 3));
        $suffix = str_pad(School::count() + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $suffix;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithValidSubscription($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('subscription_ends_at')
                ->orWhere('subscription_ends_at', '>', now());
        });
    }

    public function getActiveAcademicGroups(): BelongsToMany
    {
        return $this->academicGroups()->wherePivot('is_active', true);
    }

    public function academicGroups(): BelongsToMany
    {
        return $this->belongsToMany(AcademicGroup::class, 'school_academic_group')
            ->withPivot('is_active', 'custom_settings')
            ->withTimestamps();
    }

    public function getActiveAcademicLevels(): BelongsToMany
    {
        return $this->academicLevels()->wherePivot('is_active', true)
            ->orderBy('academic_level.sort_order');
    }

    // Scopes

    public function academicLevels(): BelongsToMany
    {
        return $this->belongsToMany(AcademicLevel::class, 'school_academic_level')
            ->withPivot('is_active', 'sort_order', 'custom_settings', 'academic_group_id')
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isUserAdmin(User $user): bool
    {
        return $user->school_id === $this->id && $user->hasRole('admin');
    }

    public function hasAcademicGroup($groupId): bool
    {
        return $this->academicGroups()->where('academic_group_id', $groupId)->exists();
    }

    public function hasAcademicLevel($levelId): bool
    {
        return $this->academicLevels()->where('academic_level_id', $levelId)->exists();
    }


    // Get admins using existing roles system

    public function getAvailableAcademicLevels()
    {
        $groupIds = $this->academicGroups()->pluck('academic_groups.id');
        return AcademicLevel::whereIn('academic_group_id', $groupIds)->get();
    }



    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    // Get available academic levels based on school's academic groups

    public function librarians(): HasMany
    {
        return $this->hasMany(Librarian::class);
    }

    // Statistics methods

    public function parents(): HasMany
    {
        return $this->hasMany(StudentParent::class);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByOwnership($query, $ownership)
    {
        return $query->where('ownership', $ownership);
    }

    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    public function currentAcademicPeriod(): HasOne
    {
        return $this->hasOne(AcademicPeriod::class)->where('is_current', true);
    }

    public function activeAcademicPeriods()
    {
        return $this->hasMany(AcademicPeriod::class)->where('status', 'active');
    }

    public function upcomingAcademicPeriods()
    {
        return $this->hasMany(AcademicPeriod::class)->where('status', 'upcoming');
    }

    // Helper methods for academic periods
    public function getCurrentPeriod(): ?AcademicPeriod
    {
        return $this->academicPeriods()->where('status', 'active')->first();
//        return $this->academicPeriods()->where('is_current', true)->first();
    }

    public function getPeriodsForYear(string $academicYear)
    {
        return $this->academicPeriods()
            ->where('academic_year', $academicYear)
            ->orderBy('year_sequence')
            ->orderBy('sequence')
            ->get();
    }

    public function createAcademicPeriod(array $data): AcademicPeriod
    {
        return $this->academicPeriods()->create($data);
    }

    public function getAvailableAcademicYears(): Collection
    {
        return $this->academicPeriods()
            ->select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');
    }

    public function hasActiveAcademicPeriod(): bool
    {
        return $this->academicPeriods()->where('status', 'active')->exists();
    }

    public function isRegistrationOpen(): bool
    {
        return $this->academicPeriods()
            ->where('status', 'upcoming')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('registration_start')
                        ->whereNotNull('registration_end')
                        ->where('registration_start', '<=', now())
                        ->where('registration_end', '>=', now());
                })->orWhere(function ($q) {
                    $q->whereNull('registration_start')
                        ->orWhereNull('registration_end');
                });
            })
            ->exists();
    }

// Add this relationship to School model

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function activeStudentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class)->where('is_active', true);
    }

// Update getStats method to include student groups
    public function getStats(): array
    {
        $currentPeriod = $this->getCurrentPeriod();

        return [
            'total_students' => $this->students()->count(),
            'active_students' => $this->students()->where('status', 'active')->count(),
            'total_teachers' => $this->teachers()->count(),
            'active_teachers' => $this->teachers()->where('status', 'active')->count(),
            'total_librarians' => $this->librarians()->count(),
            'total_parents' => $this->parents()->count(),
            'academic_groups' => $this->academicGroups()->count(),
            'academic_levels' => $this->academicLevels()->count(),
            'student_groups' => $this->studentGroups()->count(), // Add this
            'active_student_groups' => $this->activeStudentGroups()->count(), // Add this
            'current_period' => $currentPeriod ? $currentPeriod->getDisplayName() : 'No active period',
            'current_period_progress' => $currentPeriod ? round($currentPeriod->getProgressPercentage(), 1) : 0,
            'total_academic_periods' => $this->academicPeriods()->count(),
            'active_periods' => $this->academicPeriods()->where('status', 'active')->count(),
        ];
    }
}
