<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExamSubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_exam_subscription_id',
        'user_id',
        'paystack_reference',
        'paystack_access_code',
        'amount',
        'currency',
        'status',
        'payment_type',
        'additional_participants',
        'paystack_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paystack_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubscription::class, 'general_exam_subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
