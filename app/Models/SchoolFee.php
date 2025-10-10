<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolFee extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'payer_id',
        'payer_type',
        'school_name',
        'amount',
        'currency',
        'status',
        'reference',
        'authorization_url',
        'paystack_response',
    ];

    // protected $casts = [
    //     'paystack_response' => 'array',
    // ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
