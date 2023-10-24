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

    public function renewal()
    {
        return $this->belongsTo(SubscriptionRenewal::class);
    }
}
