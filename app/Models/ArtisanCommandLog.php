<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtisanCommandLog extends Model
{
    protected $fillable = [
        'user_id',
        'command',
        'arguments',
        'output',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
        'executed_at',
    ];

    protected $casts = [
        'arguments' => 'array',
        'executed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedArgumentsAttribute(): string
    {
        if (!$this->arguments) {
            return 'No arguments';
        }

        return collect($this->arguments)
            ->map(fn($value, $key) => "$key: $value")
            ->implode(', ');
    }
}
