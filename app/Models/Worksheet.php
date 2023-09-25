<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Worksheet extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'seed',
        'cursor',
        'sheets',
        'started_at',
        'ended_at',
        'user_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sheets' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected function cursor(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => explode(',', $value),
            set: fn (array $value) => implode(',', $value)
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(function (mixed $value, array $attributes) {
            return $attributes['started_at'] && $attributes['ended_at']
                ? Carbon::parse($attributes['ended_at'])->diffInMinutes($attributes['started_at'])
                : 0;
        });
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
