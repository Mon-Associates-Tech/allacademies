<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScale extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_level_id',
        'name',
        'min_score',
        'max_score',
        'letter_grade',
        'grade_point',
        'remarks',
        'is_default',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public static function getGradeLabel($score, $schoolId = null, $levelId = null)
    {
        $query = self::where('school_id', $schoolId)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score);

        if ($levelId) {
            $query->where(function ($q) use ($levelId) {
                $q->where('academic_level_id', $levelId)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('academic_level_id')->where('is_default', true);
                    });
            });
        } else {
            $query->where('is_default', true);
        }

        $scale = $query->first();

        return $scale ? $scale->letter_grade : 'N/A';
    }

    public static function getForScore($score, $schoolId, $levelId = null): ?self
    {
        $query = self::where('school_id', $schoolId)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score);

        if ($levelId) {
            $query->where(function ($q) use ($levelId) {
                $q->where('academic_level_id', $levelId)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('academic_level_id')->where('is_default', true);
                    });
            });
        } else {
            $query->where('is_default', true);
        }

        return $query->first();
    }
}
