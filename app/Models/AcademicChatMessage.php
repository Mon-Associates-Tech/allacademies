<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conversation_id',
        'conversation_title',
        'content',
        'role',
        'parameters',
        'usage'
    ];

    protected $casts = [
        'parameters' => 'array',
        'usage' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeConversations($query)
    {
        return $query->select('conversation_id', 'conversation_title', 'user_id')
                    ->whereNotNull('conversation_id')
                    ->groupBy('conversation_id', 'conversation_title', 'user_id')
                    ->orderBy('created_at', 'desc');
    }
}
