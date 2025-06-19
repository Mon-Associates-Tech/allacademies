<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'session_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
