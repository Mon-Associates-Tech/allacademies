<?php

namespace App\Models\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumMention extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentionable_type',
        'mentionable_id',
        'mentioned_user_id',
        'mentioning_user_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function mentionable()
    {
        return $this->morphTo();
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function mentioningUser()
    {
        return $this->belongsTo(User::class, 'mentioning_user_id');
    }
}
