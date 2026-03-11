<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\ActivityLoggable;
use App\Traits\BelongsToSchoolEnhanced;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicGroup extends Model
{
    use AcademicGroupLogs;
    use ActivityLoggable;
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    //    use BelongsToSchoolEnhanced;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tag',
        'modified_by',
        'added_by',
        'school_id',
    ];

    public function academicLevels(): AcademicGroup|HasMany
    {
        return $this->hasMany(AcademicLevel::class);
    }

    public function primaryTeachers(): BelongsToMany
    {
        return $this->teachers()->wherePivot('is_primary', true);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'academic_group_teacher', 'academic_group_id', 'teacher_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_academic_group')
            ->withPivot('is_active', 'custom_settings')
            ->withTimestamps();
    }

    // Students in this group (across all schools)
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // Get students for a specific school
    public function studentsForSchool($schoolId)
    {
        return $this->students()->where('school_id', $schoolId);
    }

    // Scope to get groups for a specific school
    public function scopeForSchool($query, $schoolId)
    {
        return $query->whereHas('schools', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        });
    }

    public function scopeForCurrentSchool($query)
    {
        $user = auth()->user();

        if (! $user || $user->canAccessCrossSchool()) {
            $schoolId = app()->has('current_school') ? app('current_school')->id : null;
            if ($schoolId) {
                return $query->whereHas('schools', function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId)->where('is_active', true);
                });
            }

            return $query;
        }

        return $query->whereHas('schools', function ($q) use ($user) {
            $q->where('school_id', $user->school_id)->where('is_active', true);
        });
    }
}
