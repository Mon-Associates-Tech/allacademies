<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicLevel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    use AcademicGroupLogs;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
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
}
