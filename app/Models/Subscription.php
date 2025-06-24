<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'package',
        'amount',
        'currency',
        'reference',
        'beneficiaries',
        'status',
        'expires_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => SubscriptionStatus::class,
        'expires_at' => 'datetime',
    ];

    public function subscriber()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function academicSubjects()
    {
        return $this->belongsToMany(AcademicSubject::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bookSubscriptions()
    {
        return $this->hasMany(BookSubscription::class);
    }

     public function user()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }
}
