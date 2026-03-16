<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardChangeLog extends Model
{
    protected $fillable = [
        'report_card_id',
        'report_card_grade_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(ReportCardGrade::class, 'report_card_grade_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
