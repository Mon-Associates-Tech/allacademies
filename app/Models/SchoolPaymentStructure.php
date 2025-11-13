<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolPaymentStructure extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchoolEnhanced;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_period_id',
        'academic_group_id',
        'academic_level_id',
        'name',
        'payment_type',
        'amount',
        'currency',
        'due_date',
        'payment_period',
        'is_mandatory',
        'allow_partial_payment',
        'minimum_partial_amount',
        'is_active',
        'description',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'minimum_partial_amount' => 'decimal:2',
        'due_date' => 'date',
        'is_mandatory' => 'boolean',
        'allow_partial_payment' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SchoolPayment::class, 'payment_structure_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeForAcademicPeriod($query, $periodId)
    {
        return $query->where('academic_period_id', $periodId);
    }

    public function scopeForAcademicGroup($query, $groupId)
    {
        return $query->where('academic_group_id', $groupId);
    }

    public function scopeForAcademicLevel($query, $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function isDueSoon($days = 7): bool
    {
        return $this->due_date &&
            $this->due_date->isFuture() &&
            $this->due_date->diffInDays(now()) <= $days;
    }

    public function getApplicableStudents()
    {
        $query = Student::where('school_id', $this->school_id);

        if ($this->academic_group_id) {
            $query->where('academic_group_id', $this->academic_group_id);
        }

        if ($this->academic_level_id) {
            $query->where('academic_level_id', $this->academic_level_id);
        }

        return $query->get();
    }

    public function getTotalCollected()
    {
        return $this->payments()->succeeded()->sum('amount');
    }

    public function getTotalPending()
    {
        return $this->payments()->pending()->sum('amount');
    }

    public function getCollectionRate(): float
    {
        $totalStudents = $this->getApplicableStudents()->count();
        if ($totalStudents === 0) return 0;

        $paidStudents = $this->payments()
            ->succeeded()
            ->distinct('student_id')
            ->count('student_id');

        return ($paidStudents / $totalStudents) * 100;
    }

    public static function paymentTypes(): array
    {
        return SchoolPayment::paymentTypes();
    }

    public static function paymentPeriods(): array
    {
        return SchoolPayment::paymentPeriods();
    }
}
