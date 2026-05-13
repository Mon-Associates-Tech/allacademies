<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExamConfiguredParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_exam_id',
        'name',
        'email',
        'unique_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }
}

