<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSubject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicTopics()
    {
        return $this->hasMany(AcademicTopic::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function academicGroup()
    {
        return $this->hasOneThrough(
            AcademicGroup::class,
            AcademicLevel::class,
            'id', // Foreign key on the related table, academic_levels
            'id', // Local key on this model, academic_subjects
            'academic_level_id', // Foreign key on the intermediate table, academic_levels
            'academic_group_id' // Local key on the intermediate table, academic_groups
        );
    }

    public static function belongToOneAcademicGroup($subjectIds)
    {
        $academicGroupIds = AcademicSubject::whereIn('id', $subjectIds)
            ->with('academicGroup')
            ->get()
            ->pluck('academicGroup.id')
            ->unique();

        return $academicGroupIds->count() === 1;
    }
}
