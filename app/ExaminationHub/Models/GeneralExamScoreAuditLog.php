<?php

namespace App\ExaminationHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExamScoreAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_exam_submission_id',
        'edited_by',
        'old_score',
        'new_score',
        'old_grade',
        'new_grade',
        'old_percentage',
        'new_percentage',
        'reason',
        'question_changes',
    ];

    protected function casts(): array
    {
        return [
            'old_score' => 'decimal:2',
            'new_score' => 'decimal:2',
            'old_percentage' => 'decimal:2',
            'new_percentage' => 'decimal:2',
            'question_changes' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'general_exam_submission_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
