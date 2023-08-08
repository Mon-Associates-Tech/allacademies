<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'event',
        'snapshot',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'snapshot' => 'array',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }
}
