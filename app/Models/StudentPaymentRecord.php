<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPaymentRecord extends Model
{
    use BelongsToSchoolEnhanced, HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'student_id',
        'payment_structure_id',
        'academic_year_id',
        'academic_period_id',
        'payment_type',
        'description',
        'total_amount',
        'amount_paid',
        'amount_remaining',
        'currency',
        'due_date',
        'status',
        'is_custom',
        'arrears_from_previous',
        'discount_amount',
        'waived',
        'waived_by',
        'waived_at',
        'waived_reason',
        'metadata',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'arrears_from_previous' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'due_date' => 'date',
        'waived' => 'boolean',
        'is_custom' => 'boolean',
        'waived_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentStructure(): BelongsTo
    {
        return $this->belongsTo(SchoolPaymentStructure::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SchoolPayment::class, 'student_payment_record_id');
    }

    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->where('waived', false);
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid' && !$this->waived;
    }

    public function updatePaymentStatus(): void
    {
        if ($this->waived) {
            $this->status = 'waived';
        } elseif ($this->amount_paid >= $this->total_amount) {
            $this->status = 'paid';
            $this->amount_remaining = 0;
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
            $this->amount_remaining = $this->total_amount - $this->amount_paid;
        } else {
            $this->status = 'unpaid';
            $this->amount_remaining = $this->total_amount;
        }
        
        $this->save();
    }

    public function addPayment(float $amount): void
    {
        $this->amount_paid += $amount;
        $this->updatePaymentStatus();
    }

    public function waive(User $user, ?string $reason = null): void
    {
        $this->update([
            'waived' => true,
            'waived_by' => $user->id,
            'waived_at' => now(),
            'waived_reason' => $reason,
            'status' => 'waived',
        ]);
    }
}
