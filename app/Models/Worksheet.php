<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sections' => 'array',
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

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
