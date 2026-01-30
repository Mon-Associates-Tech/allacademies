<?php

namespace App\Models;

use App\Models\Attendance\Attendance;
use App\Traits\BelongsToSchoolEnhanced;
use App\Traits\HasStudents;
use App\Traits\ShouldScopeSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;
    use HasStudents;
    use BelongsToSchoolEnhanced;
//    use ShouldScopeSchool;

    protected $fillable = [
        'school_id', 'user_id', 'employee_id', 'department',
        'hire_date', 'termination_date', 'salary',
        'employment_type', 'status', 'qualifications', 'specialization',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
        'qualifications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentsFromGroups()
    {
        return $this->hasManyThrough(Student::class, StudentGroup::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function primaryStudents()
    {
        return $this->belongsToMany(Student::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', true);
    }

    public function secondaryStudents()
    {
        return $this->belongsToMany(Student::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', false);
    }

    public function subjects()
    {
        return $this->belongsToMany(AcademicSubject::class, 'subject_teacher', 'teacher_id', 'subject_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(GroupBookSubscription::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function primaryAcademicLevels(): BelongsToMany
    {
        return $this->academicLevels()->wherePivot('is_primary', true);
    }

    public function academicLevels(): BelongsToMany
    {
        return $this->belongsToMany(AcademicLevel::class, 'academic_level_teacher', 'teacher_id', 'academic_level_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function primaryAcademicGroups(): BelongsToMany
    {
        return $this->academicGroups()->wherePivot('is_primary', true);
    }

    public function academicGroups(): BelongsToMany
    {
        return $this->belongsToMany(AcademicGroup::class, 'academic_group_teacher', 'teacher_id', 'academic_group_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    // Helper methods for primary relationships

    public function academicSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function assignments(): HasMany|Teacher
    {
        return $this->hasMany(Assignment::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'teacher_book', 'teacher_id', 'book_id');
    }

    public function attendances(): HasMany|Teacher
    {
        return $this->hasMany(Attendance::class);
    }

    public function getStudentsForAcademicLevel($academicLevelId)
    {
        return $this->assignedStudents()
            ->where('academic_level_id', $academicLevelId)
            ->with('user')
            ->get();
    }

    public function assignedStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function canTakeAttendanceForLevel($academicLevelId): bool
    {
        return $this->academicLevels()
            ->where('academic_levels.id', $academicLevelId)
            ->exists();
    }

    // Access control methods
    public function hasAccessToStudent(Student $student): bool
    {
        // Check if same school
        if ($this->school_id !== $student->school_id) {
            return false;
        }

        // Check direct assignments
        if ($this->assignedStudents()->where('teacher_student.student_id', $student->id)->exists()) {
            return true;
        }

        // Check academic level assignments
        if ($this->academicLevels()
            ->whereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })->exists()) {
            return true;
        }

        // Check academic group assignments
        if ($this->academicGroups()
            ->whereHas('academicLevels.students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })->exists()) {
            return true;
        }

        return false;
    }

    public function studentGroups(): HasMany|Teacher
    {
        return $this->hasMany(StudentGroup::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Generate school-specific employee ID
    public static function generateEmployeeId($schoolId)
    {
        $school = School::find($schoolId);

        $lastTeacher = static::withoutGlobalScope('school')
            ->where('school_id', $schoolId)
            ->where('employee_id', 'like', "{$school->code}T%")
            ->latest('employee_id')
            ->first();

        $sequence = $lastTeacher ?
            (int) substr($lastTeacher->employee_id, -4) + 1 : 1;

        return $school->code.'T'.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
