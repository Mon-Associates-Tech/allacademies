<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchPaymentAccount extends Model
{
    use HasFactory;

    protected $table = 'bookshop_branch_payment_accounts';

    protected $fillable = [
        'branch_id',
        'subaccount_code',
        'business_name',
        'settlement_bank',
        'bank_code',
        'account_number',
        'percentage_charge',
        'paystack_response',
        'is_active',
        'updated_by_staff_id',
    ];

    protected $casts = [
        'percentage_charge' => 'decimal:2',
        'paystack_response' => 'array',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by_staff_id');
    }

    public function isReadyForPayments(): bool
    {
        return $this->is_active && ! empty($this->subaccount_code);
    }
}
