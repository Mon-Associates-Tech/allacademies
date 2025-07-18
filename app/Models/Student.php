<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['user_id', 'student_group_id', 'academic_level_id', 'academic_group_id', 'school_id'];

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'student_group_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentGroup(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    public function borrowedBooks(): Student|HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function subscriptions(): Student|HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function primaryTeacher()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', true)
            ->first();
    }

    public function secondaryTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', false);
    }

    public function enrollments()
    {
        //return $this->hasMany(Enrollment::class);
    }

    public function assessments(): Student|HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * The books that this student has access to.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->withPivot('access_granted_at', 'access_expires_at')
            ->withTimestamps();
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    // Individual subject assignments (overrides/additions to academic level subjects)
    public function individualSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'student_subject')
            ->withTimestamps()
            ->withPivot('is_active', 'assigned_by', 'notes', 'assigned_at');
    }

    // Main relationship for academic subjects (for eager loading compatibility)
    public function academicSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'student_subject')
            ->withTimestamps()
            ->withPivot('is_active', 'assigned_by', 'notes', 'assigned_at');
    }

    // Get subjects from academic level (as relationship)
    public function levelSubjects()
    {
        return $this->hasManyThrough(
            AcademicSubject::class,
            AcademicLevel::class,
            'id', // Foreign key on AcademicLevel table
            'academic_level_id', // Foreign key on AcademicSubject table
            'academic_level_id', // Local key on Student table
            'id' // Local key on AcademicLevel table
        );
    }

    // Alternative approach - get level subjects via academic level relationship
    public function subjectsFromLevel()
    {
        return $this->academicLevel()->with('academicSubjects');
    }

    // Helper methods that return collections (not relationships)
    public function getAllAccessibleSubjects()
    {
        $levelSubjects = collect();
        $individualSubjects = $this->individualSubjects;

        // Get subjects from academic level
        if ($this->academicLevel) {
            $levelSubjects = $this->academicLevel->academicSubjects;
        }

        // Merge with individual assignments, removing duplicates and respecting is_active status
        $allSubjects = $levelSubjects->keyBy('id');

        foreach ($individualSubjects as $subject) {
            if ($subject->pivot->is_active === false) {
                // Add or keep the subject
                $allSubjects[$subject->id] = $subject;
            } else {
                // Remove the subject if it's marked as inactive (override from level)
//                $allSubjects->forget($subject->id);
            }
        }

        return $allSubjects->values();
    }

    public function getIndividuallyAssignedSubjects()
    {
        return $this->individualSubjects()
            ->wherePivot('is_active', true)
            ->whereNotIn('academic_subjects.id', function($query) {
                if ($this->academicLevel) {
                    $query->select('id')
                        ->from('academic_subjects')
                        ->where('academic_level_id', $this->academicLevel->id);
            } else {
                $query->selectRaw('NULL')->whereRaw('1=0');
            }
        })
        ->get();
    }

    public function getRemovedLevelSubjects()
    {
        return $this->individualSubjects()
            ->wherePivot('is_active', false)
            ->get();
    }

    // Accessor for getting all accessible subjects as an attribute
    public function getAccessibleSubjectsAttribute()
    {
        return $this->getAllAccessibleSubjects();
    }

    // Legacy method for backward compatibility - now returns all accessible subjects
    public function academicSdubjects()
    {
        return $this->getAllAccessibleSubjects();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function bookSubscriptions(): Student|HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    // Add this method to get detailed subject information
    public function getSubjectDetails()
    {
        $details = [
            'level_subjects' => collect(),
            'individual_active' => collect(),
            'individual_removed' => collect(),
            'total_accessible' => collect()
        ];

        // Get subjects from academic level
        if ($this->academicLevel) {
            $details['level_subjects'] = $this->academicLevel->academicSubjects;
        }

        // Get individual assignments
        $individualSubjects = $this->individualSubjects;
        $details['individual_active'] = $individualSubjects->where('pivot.is_active', true);
        $details['individual_removed'] = $individualSubjects->where('pivot.is_active', false);

        // Calculate total accessible subjects
        $allSubjects = $details['level_subjects']->keyBy('id');

        // Add individual active subjects
        foreach ($details['individual_active'] as $subject) {
            $allSubjects[$subject->id] = $subject;
        }

        // Remove individual removed subjects
        foreach ($details['individual_removed'] as $subject) {
            $allSubjects->forget($subject->id);
        }

        $details['total_accessible'] = $allSubjects->values();

        return $details;
    }

    public function libraryCard()
    {
        return $this->hasOne(LibraryCard::class);
    }

    public function libraryCards()
    {
        return $this->hasMany(LibraryCard::class);
    }

    public function activeLibraryCard()
    {
        return $this->hasOne(LibraryCard::class)->where('status', 'active');
    }

    public function getCanBorrowBooksAttribute()
    {
        $activeCard = $this->activeLibraryCard;
        return $activeCard && $activeCard->can_borrow;
    }

}
