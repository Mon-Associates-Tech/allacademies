<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class StudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'school_id',
        'academic_group_id',
        'academic_level_id',
        'academic_subject_id',
        'teacher_id',
        'creator_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GroupBookSubscription::class);
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(Assignment::class, 'assignment_student_group');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForAcademicGroup(Builder $query, int $academicGroupId): Builder
    {
        return $query->where('academic_group_id', $academicGroupId);
    }

    public function scopeForAcademicLevel(Builder $query, int $academicLevelId): Builder
    {
        return $query->where('academic_level_id', $academicLevelId);
    }

    public function scopeForAcademicSubject(Builder $query, int $academicSubjectId): Builder
    {
        return $query->where('academic_subject_id', $academicSubjectId);
    }

    public function scopeForTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeWithStudentCount(Builder $query): Builder
    {
        return $query->withCount('students');
    }

    // Helper Methods

    public function getActiveStudentsCount(): int
    {
        return $this->students()->where('status', 'active')->count();
    }

    public function hasTeacher(): bool
    {
        return !is_null($this->teacher_id);
    }

    public function belongsToAcademicGroup(): bool
    {
        return !is_null($this->academic_group_id);
    }

    public function belongsToAcademicLevel(): bool
    {
        return !is_null($this->academic_level_id);
    }

    public function belongsToAcademicSubject(): bool
    {
        return !is_null($this->academic_subject_id);
    }

    public function getDisplayName(): string
    {
        $name = $this->name;

        if ($this->academicLevel) {
            $name .= ' - ' . $this->academicLevel->name;
        }

        if ($this->academicSubject) {
            $name .= ' (' . $this->academicSubject->name . ')';
        }

        return $name;
    }

    public function getFullDescription(): string
    {
        $parts = [$this->name];

        if ($this->academicGroup) {
            $parts[] = 'Group: ' . $this->academicGroup->name;
        }

        if ($this->academicLevel) {
            $parts[] = 'Level: ' . $this->academicLevel->name;
        }

        if ($this->academicSubject) {
            $parts[] = 'Subject: ' . $this->academicSubject->name;
        }

        if ($this->teacher) {
            $parts[] = 'Teacher: ' . $this->teacher->user->name;
        }

        return implode(' | ', $parts);
    }

    // Check if a user can access this group
    public function canUserAccess(User $user): bool
    {
        // School admin can access all groups in their school
        if ($user->school_id === $this->school_id && $user->hasRole('admin')) {
            return true;
        }

        // Teacher assigned to this group
        if ($user->teacher && $user->teacher->id === $this->teacher_id) {
            return true;
        }

        // Student in this group
        if ($user->student && $user->student->student_group_id === $this->id) {
            return true;
        }

        return false;
    }
}
