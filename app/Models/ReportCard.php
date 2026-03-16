<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCard extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'term',
        'school_id',
        'report_card_configuration_id',
        'status',
        'generated_at',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'is_accessible',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_accessible' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(ReportCardConfiguration::class, 'report_card_configuration_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(ReportCardGrade::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(ReportCardChangeLog::class);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->hasRole('admin')) {
            return true;
        }

        if ($this->status === 'approved' || $this->status === 'published') {
            return false;
        }

        return true;
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->changeLogs()->create([
            'user_id' => auth()->id(),
            'action' => 'submitted',
        ]);
    }

    public function approve(User $user): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
            'is_accessible' => true,
        ]);

        $this->changeLogs()->create([
            'user_id' => $user->id,
            'action' => 'approved',
        ]);
    }

    public function reject(User $user, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $this->changeLogs()->create([
            'user_id' => $user->id,
            'action' => 'rejected',
            'notes' => $reason,
        ]);
    }
}
