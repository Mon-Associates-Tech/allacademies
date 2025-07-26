<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $table = 'login_activities';
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'session_id',
        'login_at',
        'logout_at',
        'duration_minutes',
        'logout_type'
    ];

    /**
     * Calculate the session duration.
     */
    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logout_at
                ? $this->created_at->diffForHumans($this->logout_at, true)
                : 'Online'
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
