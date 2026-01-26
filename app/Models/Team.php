<?php

namespace App\Models;

use App\Enums\TeamStatus;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_personal',
        'meta',
        'status',
        'declined_reason',
        'joining_code',
        'owner_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TeamStatus::class,
        'meta' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
