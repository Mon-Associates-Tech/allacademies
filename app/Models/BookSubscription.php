<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BookSubscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'student_id',
        'book_id',
        'start_date',
        'end_date',
        'status',
        'reference',
        'annual_fee',
        'payment_completed_at'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'payment_completed_at' => 'datetime',
        'annual_fee' => 'decimal:2'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'start_date', 'end_date', 'annual_fee', 'payment_completed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }
}
