<?php

namespace App\MockExam\Models;

use App\ExaminationHub\Models\GeneralExamSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamSubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_subscription_id',
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
        return $this->belongsTo(MockExamSubscription::class, 'mock_exam_subscription_id');
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
