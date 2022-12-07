<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

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
}
