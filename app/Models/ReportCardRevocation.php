<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardRevocation extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'report_card_configuration_id',
        'student_id',
        'academic_subject_id',
        'revocation_type',
        'reason',
        'revoked_by',
    ];

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(ReportCardConfiguration::class, 'report_card_configuration_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }
}
