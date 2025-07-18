<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LibraryCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'card_number',
        'barcode',
        'card_type',
        'status',
        'issued_date',
        'expiry_date',
        'suspended_at',
        'suspended_by',
        'renewed_at',
        'renewed_by',
        'issued_by',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'datetime',
        'expiry_date' => 'datetime',
        'suspended_at' => 'datetime',
        'renewed_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_EXPIRED = 'expired';

    const TYPE_STUDENT = 'student';
    const TYPE_PREMIUM = 'premium';

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }

    public function borrowedBooks()
    {
        return $this->hasMany(BorrowedBook::class);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE && !$this->is_expired;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getCanBorrowAttribute()
    {
        return $this->is_active && !$this->is_expired;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>=', now());
    }

    // Methods
    public function suspend($reason = null)
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
            'notes' => $this->notes . ($reason ? "\nSuspended: {$reason}" : ''),
        ]);
    }

    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);
    }

    public function renew($months = 12)
    {
        $this->update([
            'expiry_date' => $this->expiry_date->addMonths($months),
            'status' => self::STATUS_ACTIVE,
            'renewed_at' => now(),
            'renewed_by' => auth()->id(),
        ]);
    }

    public function markExpired()
    {
        if ($this->is_expired) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    // Boot method to handle automatic status updates
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($card) {
            // Auto-mark as expired if past expiry date
            if ($card->expiry_date && $card->expiry_date->isPast()) {
                $card->status = self::STATUS_EXPIRED;
            }
        });
    }
}
