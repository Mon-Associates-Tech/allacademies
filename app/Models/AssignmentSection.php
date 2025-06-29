<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'title',
        'instructions',
        'question_type', // 'multiple_choice', 'true_false', 'essay'
        'question_count',
        'marks_per_question',
        'order',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }
}
