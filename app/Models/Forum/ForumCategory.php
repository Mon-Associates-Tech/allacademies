<?php

namespace App\Models\Forum;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'color',
        'icon',
        'sort_order',
        'is_active',
        'is_private',
        'parent_id',
        'academic_level_id',
        'academic_subject_id',
        'book_category_id',
        'required_role',
        'moderator_ids',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'moderator_ids' => 'array',
    ];

    protected $with = [
        'latestPost',
    ];

    public function parent()
    {
        return $this->belongsTo(ForumCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function topics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function posts()
    {
        return $this->hasManyThrough(ForumPost::class, ForumTopic::class);
    }

    public function latestPost()
    {
        return $this->hasOneThrough(ForumPost::class, ForumTopic::class)->latest();
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function bookCategory()
    {
        return $this->belongsTo(BookCategory::class);
    }

    public function moderators()
    {
        return $this->belongsToMany(User::class, 'forum_category_moderators');
    }

    public function isModerator(User $user): bool
    {
        return $this->moderators()->where('user_id', $user->id)->exists() ||
               $user->hasRole(['administrator', 'super_admin']);
    }

    public function canAccess(User $user): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->is_private && ! $this->isModerator($user)) {
            return false;
        }

        if ($this->required_role && ! $user->hasRole($this->required_role)) {
            return false;
        }

        return true;
    }
}
