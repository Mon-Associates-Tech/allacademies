<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'min_score',
        'max_score',
        'letter_grade',
        'grade_point',
        'remarks'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Method to get grade label based on score
    public static function getGradeLabel($score, $schoolId = null)
    {
        $scale = self::where('school_id', $schoolId)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        return $scale ? $scale->letter_grade : 'N/A';
    }
}

