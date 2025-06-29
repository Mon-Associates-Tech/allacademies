<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type', // 'quiz' or 'examination'
        'academic_subject_id',
        'teacher_id',
        'duration_in_minutes',
        'starts_at',
        'ends_at',
        'is_randomized', // whether students get different questions
        'status', // 'draft', 'published', 'completed'
        'instructions',
        'total_marks',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_randomized' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    // Assignment can be assigned to academic groups
    public function academicGroups(): BelongsToMany
    {
        return $this->belongsToMany(AcademicGroup::class, 'assignment_academic_group');
    }

    // Assignment can be assigned to academic levels
    public function academicLevels(): BelongsToMany
    {
        return $this->belongsToMany(AcademicLevel::class, 'assignment_academic_level');
    }

    // Assignment can be assigned to specific students
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'assignment_student');
    }

    // Assignment can be assigned to student groups
    public function studentGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudentGroup::class, 'assignment_student_group');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(AcademicTopic::class, 'assignment_topic');
    }

    public function subtopics(): BelongsToMany
    {
        return $this->belongsToMany(Subtopic::class, 'assignment_subtopic');
    }

    public function assignmentSections(): HasMany
    {
        return $this->hasMany(AssignmentSection::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AssignmentNotification::class);
    }

    // Get all students who should receive this assignment
    public function getEligibleStudents()
    {
        $students = collect();

        // From academic groups
        foreach ($this->academicGroups as $group) {
            $students = $students->merge($group->students);
        }

        // From academic levels
        foreach ($this->academicLevels as $level) {
            $students = $students->merge($level->students);
        }

        // From student groups
        foreach ($this->studentGroups as $group) {
            $students = $students->merge($group->students);
        }

        // Directly assigned students
        $students = $students->merge($this->students);

        return $students->unique('id');
    }
}
