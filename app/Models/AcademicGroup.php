<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    use AcademicGroupLogs;
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

// Update the existing teachers relationship to use the pivot table
public function teachers(): BelongsToMany
{
    return $this->belongsToMany(Teacher::class, 'academic_group_teacher', 'academic_group_id', 'teacher_id')
        ->withTimestamps()
        ->withPivot('is_primary', 'notes');
}

public function primaryTeachers(): BelongsToMany
{
    return $this->teachers()->wherePivot('is_primary', true);
}
}
