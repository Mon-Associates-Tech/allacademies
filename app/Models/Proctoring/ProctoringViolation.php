<?php

namespace App\Models\Proctoring;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProctoringViolation extends Model
{
    protected $fillable = ['proctoring_session_id', 'type', 'metadata', 'severity', 'occurred_at'];
    protected $casts = ['metadata' => 'array', 'occurred_at' => 'datetime'];

    public function session(): BelongsTo { return $this->belongsTo(ExamProctoringSession::class, 'proctoring_session_id'); }
}
