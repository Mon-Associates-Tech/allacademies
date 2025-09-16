<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'currency',
        'reference',
        'status',
        'subscription_id',
        'book_subscription_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }


    public function bookSubscription()
    {
        return $this->belongsTo(BookSubscription::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
