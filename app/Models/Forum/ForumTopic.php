<?php

namespace App\Models\Forum;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\Book;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'forum_category_id',
        'user_id',
        'is_pinned',
        'is_locked',
        'is_announcement',
        'views_count',
        'last_activity_at',
        'tags',
        'academic_level_id',
        'academic_subject_id',
        'academic_topic_id',
        'study_group_id',
        'referenced_book_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_announcement' => 'boolean',
        'last_activity_at' => 'datetime',
        'tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class)->latest();
    }

    public function firstPost()
    {
        return $this->hasOne(ForumPost::class)->oldest();
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function academicTopic()
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudentGroup::class, 'study_group_id');
    }

    public function referencedBook()
    {
        return $this->belongsTo(Book::class, 'referenced_book_id');
    }

    public function attachments()
    {
        return $this->morphMany(ForumAttachment::class, 'attachable');
    }

    public function mentions()
    {
        return $this->morphMany(ForumMention::class, 'mentionable');
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }
}
