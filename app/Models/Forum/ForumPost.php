<?php

namespace App\Models\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'forum_topic_id',
        'user_id',
        'parent_id',
        'is_answer',
        'is_approved',
        'likes_count',
        'dislikes_count',
        'edited_at',
        'edited_by',
        'edit_reason'
    ];

    protected $casts = [
        'is_answer' => 'boolean',
        'is_approved' => 'boolean',
        'edited_at' => 'datetime'
    ];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function attachments()
    {
        return $this->morphMany(ForumAttachment::class, 'attachable');
    }

    public function mentions()
    {
        return $this->morphMany(ForumMention::class, 'mentionable');
    }

    public function likes()
    {
        return $this->morphMany(ForumReaction::class, 'reactable')->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->morphMany(ForumReaction::class, 'reactable')->where('type', 'dislike');
    }

    public function reactions()
    {
        return $this->morphMany(ForumReaction::class, 'reactable');
    }

    public function hasUserLiked(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function hasUserDisliked(User $user): bool
    {
        return $this->dislikes()->where('user_id', $user->id)->exists();
    }
}
