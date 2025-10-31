<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subaccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'subaccount_code',
        'business_name',
        'settlement_bank',
        'account_number',
        'percentage_charge',
        'description',
        'paystack_response',
    ];

    protected $casts = [
        'paystack_response' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
