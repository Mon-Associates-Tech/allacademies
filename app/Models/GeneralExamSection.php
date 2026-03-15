<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralExamSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_exam_id',
        'title',
        'description',
        'instructions',
        'order',
        'time_limit_minutes',
        'total_marks',
        'is_randomized',
    ];

    protected function casts(): array
    {
        return [
            'is_randomized' => 'boolean',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(GeneralExamQuestion::class)->orderBy('order');
    }

    public function recalculateTotalMarks(): void
    {
        $total = $this->questions()->sum('marks');
        $this->update(['total_marks' => $total]);
    }

    public function getQuestionsForDisplay(): \Illuminate\Database\Eloquent\Collection
    {
        $questions = $this->questions;

        if ($this->is_randomized) {
            return $questions->shuffle();
        }

        return $questions;
    }
}
