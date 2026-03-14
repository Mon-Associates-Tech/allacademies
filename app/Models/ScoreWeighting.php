<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreWeighting extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_level_id',
        'academic_subject_id',
        'name',
        'weight_percentage',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public static function getForLevel($levelId, $subjectId = null)
    {
        $query = static::where('academic_level_id', $levelId)
            ->orWhere(function ($q) use ($levelId) {
                $q->whereNull('academic_level_id')->where('is_default', true);
            });

        if ($subjectId) {
            $query->where(function ($q) use ($subjectId) {
                $q->where('academic_subject_id', $subjectId)
                    ->orWhereNull('academic_subject_id');
            });
        }

        return $query->orderBy('sort_order')->get();
    }
}
