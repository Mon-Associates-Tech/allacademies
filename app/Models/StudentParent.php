<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentParent extends Model
{
    use BelongsToSchoolEnhanced;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'relationship',
        'parent_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function accessibleStudents(): BelongsToMany
    {
        return $this->students()->where('students.school_id', $this->school_id);
    }

    public static function generateParentCode($schoolId)
    {
        $school = School::find($schoolId);
        if (! $school) {
            return null;
        }

        $lastParent = static::withoutGlobalScope('school')
            ->where('school_id', $schoolId)
            ->latest('id')
            ->first();

        $sequence = $lastParent ? $lastParent->id + 1 : 1;

        return $school->code.'P'.str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
