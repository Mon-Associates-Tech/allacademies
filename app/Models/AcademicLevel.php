<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\ActivityLoggable;
use App\Traits\BelongsToSchoolEnhanced;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicLevel extends Model
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
        'label',
        'school_id',
        'academic_group_id',

    ];

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicSubjects(): AcademicLevel|HasMany
    {
        return $this->hasMany(AcademicSubject::class);
    }

    public function students(): AcademicLevel|HasMany
    {
        return $this->hasMany(Student::class);
    }

    // Update the existing teachers relationship to use the pivot table
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'academic_level_teacher', 'academic_level_id', 'teacher_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function primaryTeachers(): BelongsToMany
    {
        return $this->teachers()->wherePivot('is_primary', true);
    }

    public function subjects(): AcademicLevel|HasMany
    {
        return $this->hasMany(AcademicSubject::class);
    }

    // Get active students count
    public function getActiveStudentsCountAttribute()
    {
        return $this->students()->where('status', 'active')->count();
    }

    // Scope for levels with students
    public function scopeWithStudents($query)
    {
        return $query->has('students');
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'school_academic_level')
            ->withPivot('is_active', 'sort_order', 'custom_settings')
            ->withTimestamps();
    }

    // Get students for a specific school
    public function studentsForSchool($schoolId)
    {
        return $this->students()->where('school_id', $schoolId);
    }

    // Scope to get levels for a specific school
    public function scopeForSchool($query, $schoolId)
    {
        return $query->whereHas('schools', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        });
    }

    // Scope to get levels for specific academic groups
    public function scopeForAcademicGroups($query, array $groupIds)
    {
        return $query->whereIn('academic_group_id', $groupIds);
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
