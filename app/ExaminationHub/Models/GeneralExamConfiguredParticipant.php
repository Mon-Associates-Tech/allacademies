<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExamConfiguredParticipant extends Model
{
    use HasFactory, \App\ExaminationHub\Traits\HasUuid;

    protected $fillable = [
        'uuid',
        'general_exam_id',
        'center_id',
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

    public function center(): BelongsTo
    {
        return $this->belongsTo(\App\ExaminationHub\Models\Center::class, 'center_id');
    }
}

