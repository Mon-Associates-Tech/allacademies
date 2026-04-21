<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCardConfiguration extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_period_id',
        'academic_level_id',
        'report_card_template_id',
        'requires_approval',
        'is_published',
        'available_from',
        'available_until',
        'preparation_mode',
        'principal_name',
        'principal_signature_path',
        'class_teacher_name',
        'class_teacher_signature_path',
        'min_subjects',
        'max_subjects',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'min_subjects' => 'integer',
        'max_subjects' => 'integer',
    ];

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportCardTemplate::class, 'report_card_template_id');
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    public function revocations(): HasMany
    {
        return $this->hasMany(ReportCardRevocation::class);
    }

    public function isAccessible(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    public function isAccessibleForStudent($studentId): bool
    {
        if (!$this->isAccessible()) {
            return false;
        }

        return !$this->revocations()
            ->where('student_id', $studentId)
            ->where('revocation_type', 'student')
            ->exists();
    }
}
