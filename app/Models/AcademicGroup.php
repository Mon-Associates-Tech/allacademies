<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function academicLevels()
    {
        return $this->hasMany(AcademicLevel::class);
    }

    public function academicSubjects()
    {
        return $this->hasManyThrough(AcademicSubject::class, AcademicLevel::class, 'academic_group_id', 'academic_level_id');
    }
}
