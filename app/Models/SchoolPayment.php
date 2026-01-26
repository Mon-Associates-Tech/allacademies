<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SchoolPayment extends Model
{
    use BelongsToSchoolEnhanced, HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'student_id',
        'financial_aid_id',
        'academic_group_id',
        'academic_level_id',
        'academic_year_id',
        'academic_period_id',
        'payment_type',
        'amount',
        'fixed_amount',
        'currency',
        'payment_period',
        'payer_type',
        'payer_id',
        'payer_name',
        'payer_email',
        'payer_phone',
        'status',
        'reference',
        'transaction_id',
        'payment_method',
        'gateway',
        'authorization_url',
        'gateway_response',
        'description',
        'metadata',
        'paid_at',
        'created_by',
        'verified_by',
        'verified_at',
        'payment_structure_id',
        'subaccount_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'verified_at',
        'deleted_at',
    ];

    // Boot method to auto-generate reference
    protected static function boot()
    {
        parent::boot();

        Relation::morphMap([
            'parent' => StudentParent::class,
            'student' => Student::class,
            'other' => User::class,
        ]);

        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $payment->reference = $payment->generateReference();
            }

            // Auto-populate academic context from student if not provided
            if ($payment->student_id && ! $payment->academic_group_id) {
                $student = Student::find($payment->student_id);
                if ($student) {
                    $payment->academic_group_id = $student->academic_group_id;
                    $payment->academic_level_id = $student->academic_level_id;
                }
            }
        });
    }

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function payer()
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function schoolPaymentType(): BelongsTo
    {
        return $this->belongsTo(SchoolPaymentStructure::class);
    }

    public function subaccount(): BelongsTo
    {
        return $this->belongsTo(Subaccount::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSucceeded($query)
    {
        return $query->where('status', 'succeeded');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForAcademicGroup($query, $groupId)
    {
        return $query->where('academic_group_id', $groupId);
    }

    public function scopeForAcademicLevel($query, $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    public function scopeForAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeForAcademicPeriod($query, $periodId)
    {
        return $query->where('academic_period_id', $periodId);
    }

    public function scopeByPayerType($query, $payerType)
    {
        return $query->where('payer_type', $payerType);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Helper methods
    public function generateReference(): string
    {
        $prefix = 'PAY';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsSucceeded(array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => 'succeeded',
            'paid_at' => now(),
        ], $additionalData));
    }

    public function markAsFailed(array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => 'failed',
        ], $additionalData));
    }

    public function verify(User $verifier): bool
    {
        return $this->update([
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);
    }

    public function isCustomAmount(): bool
    {
        return $this->fixed_amount && $this->amount != $this->fixed_amount;
    }

    public function getPayerDisplayName(): string
    {
        if ($this->payer_type === 'other' && $this->payer_name) {
            return $this->payer_name;
        }

        if ($this->payer) {
            // For parent payers, get the user's name through the relationship
            if ($this->payer_type === 'parent' && isset($this->payer->user)) {
                return $this->payer->user->name ?? 'Unknown Payer';
            }

            // For student and other payers, directly access the name
            return $this->payer->name ?? 'Unknown Payer';
        }

        // Fallback to payer_name or payer_email if relationship failed
        return $this->payer_name ?? $this->payer_email ?? 'Unknown Payer';
    }

    // Payment type constants
    public static function paymentTypes(): array
    {
        return [
            'tuition' => 'Tuition Fee',
            'library' => 'Library Fee',
            'transport' => 'Transport Fee',
            'uniform' => 'Uniform Fee',
            'exam' => 'Examination Fee',
            'sports' => 'Sports Fee',
            'pta' => 'PTA Dues',
            'development' => 'Development Levy',
            'technology' => 'Technology Fee',
            'lab' => 'Laboratory Fee',
            'other' => 'Other',
        ];
    }

    public static function paymentPeriods(): array
    {
        return [
            'term_1' => 'Term 1',
            'term_2' => 'Term 2',
            'term_3' => 'Term 3',
            'semester_1' => 'Semester 1',
            'semester_2' => 'Semester 2',
            'annual' => 'Annual',
            'monthly' => 'Monthly',
            'one_time' => 'One Time',
            'other' => 'Other',
        ];
    }

    public static function payerTypes(): array
    {
        return [
            'parent' => 'Parent/Guardian',
            'student' => 'Student',
            'other' => 'Other',
        ];
    }

    public function financialAid()
    {
        return $this->belongsTo(FinancialAid::class);
    }
}
