<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'payroll_entry_id',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'paystack_recipient_code',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrollEntry(): BelongsTo
    {
        return $this->belongsTo(PayrollEntry::class);
    }

    public function getMaskedAccountNumberAttribute(): string
    {
        return '****' . substr($this->account_number, -4);
    }
}
