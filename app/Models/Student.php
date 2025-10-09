<?php

namespace App\Models;

use App\Models\Attendance\AttendanceRecord;
use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Student extends Model
{
    use HasFactory, LogsActivity;
    use BelongsToSchoolEnhanced;

    protected $fillable = [
        'school_id', 'user_id', 'student_id', 'student_group_id',
        'academic_level_id', 'academic_group_id', 'admission_date',
        'graduation_date', 'status', 'metadata',
        //
        'date_of_birth',
        'blood_group',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'emergency_contact',
        'id_card_issue_date',
        'id_card_expiry_date'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'graduation_date' => 'date',
        'metadata' => 'array'
    ];

    protected $with = [
        'user',
        'user',
        'academicLevel',
        'academicGroup'
    ];

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'student_group_id', 'academic_level_id', 'status'])
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

    public function borrowedBooks()
    {
        return $this->hasManyThrough(
            BookBorrowing::class,  // The final model we want to access
            User::class,           // The intermediate model
            'id',                  // Foreign key on the intermediate model (users table)
            'user_id',             // Foreign key on the final model (book_borrowings table)
            'user_id',             // Local key on the Student model
            'id'                   // Local key on the intermediate model (User)
        );
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

    public function primaryTeacher(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', true);
    }

    public function getPrimaryTeacherAttribute()
    {
        return $this->primaryTeacher()->first();
    }
    public function secondaryTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes')
            ->wherePivot('is_primary', false);
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

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'student_subject')
            ->withTimestamps()
            ->withPivot('is_active', 'assigned_by', 'notes', 'assigned_at');
    }


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


    public function subjectsFromLevel(): BelongsTo|Builder
    {
        return $this->academicLevel()->with('academicSubjects');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function getIndividuallyAssignedSubjects()
    {
        $query = $this->individualSubjects()
            ->wherePivot('is_active', true);

        if ($this->academicLevel) {
            $query->whereNotIn('academic_subjects.id', function ($subquery) {
                $subquery->select('id')
                    ->from('academic_subjects')
                    ->where('academic_level_id', $this->academicLevel->id);
            });
        }

        return $query->get();
    }


    public function individualSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubject::class, 'student_subject')
            ->withTimestamps()
            ->withPivot('is_active', 'assigned_by', 'notes', 'assigned_at');
    }

    public function getRemovedLevelSubjects()
    {
        return $this->individualSubjects()
            ->wherePivot('is_active', false)
            ->get();
    }

    public function getAccessibleSubjectsAttribute()
    {
        return $this->getAllAccessibleSubjects();
    }


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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function getSchoolAttribute()
    {
        // First check if student's user belongs to a team
        if ($this->user) {
           // return $this->user->currentTeam;
        }

        // Fallback to actual school if no team exists
        return $this->getRelationValue('school');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
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

    public function libraryCard(): Student|HasOne
    {
        return $this->hasOne(LibraryCard::class);
    }

    public function libraryCards(): Student|HasMany
    {
        return $this->hasMany(LibraryCard::class);
    }

    public function activeLibraryCard(): Student|HasOne
    {
        return $this->hasOne(LibraryCard::class)->where('status', 'active');
    }

    public function getCanBorrowBooksAttribute(): bool
    {
        $activeCard = $this->activeLibraryCard;
        return $activeCard && $activeCard->can_borrow;
    }

    // Many-to-many relationship with parents through pivot table
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(StudentParent::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function getAttendanceForDate($date, $academicLevelId = null): ?Student
    {
        $query = $this->attendanceRecords()
            ->whereHas('attendance', function ($query) use ($date) {
                $query->where('date', $date);
            });

        if ($academicLevelId) {
            $query->whereHas('attendance', function ($query) use ($academicLevelId) {
                $query->where('academic_level_id', $academicLevelId);
            });
        }

        return $query->first();
    }

    public function attendanceRecords(): Student|HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function assignmentSubmissions(): Student|HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function academicHistory(): Student|HasMany
    {
        return $this->hasMany(AcademicHistory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByAcademicLevel($query, $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    public function scopeByAcademicGroup($query, $groupId)
    {
        return $query->where('academic_group_id', $groupId);
    }

// Generate school-specific student ID
public static function generateStudentId($schoolId = null): string
{
    $school = null;
    if(!empty($schoolId)){
        $school = School::find($schoolId);
    }


    // Handle case where school doesn't exist or doesn't have proper attributes
    $schoolCode = 'SCH';
    if ($school) {
        // Try to get school code, fallback to name, then to generic code
        if (!empty($school->code)) {
            $schoolCode = substr(strtoupper($school->code), 0, 3);
        } elseif (!empty($school->name)) {
            $schoolCode = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $school->name)), 0, 3);
            // Ensure we have at least 3 characters
            $schoolCode = str_pad($schoolCode, 3, 'X', STR_PAD_RIGHT);
        }
    }

    $year = date('Y');

    try {
        $lastStudent = static::withoutGlobalScope('school')
            ->where('school_id', $schoolId)
            ->where('student_id', 'like', "{$schoolCode}{$year}%")
            ->latest('student_id')
            ->first();

        $sequence = $lastStudent ?
            (int)substr($lastStudent->student_id, -4) + 1 : 1;

        $studentId = $schoolCode . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    } catch (\Exception $e) {
        // Fallback if there's any database issue
        $studentId = $schoolCode . $year . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    return $studentId;
}


    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('0=1');
        }

        if ($user->canAccessCrossSchool()) {
            $schoolId = app()->has('current_school') ? app('current_school')->id : null;
            return $schoolId ? $query->where('school_id', $schoolId) : $query;
        }

        return $query->where('school_id', $user->school_id);
    }

public function academicProgression(): Student|HasMany
{
    return $this->hasMany(StudentAcademicProgression::class)->orderBy('start_date');
}

public function currentAcademicLevel(): BelongsTo
{
    return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
}

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function idCards(){
        return $this->hasMany(StudentIdCard::class);
    }
}
