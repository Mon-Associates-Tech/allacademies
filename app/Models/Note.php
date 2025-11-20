<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'book_id',
        'academic_subject_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'note_shares', 'note_id', 'shared_with_user_id')
            ->withPivot('can_edit')
            ->withTimestamps();
    }

    public function shares()
    {
        return $this->hasMany(NoteShare::class);
    }

    public function isSharedWith($userId)
    {
        return $this->shares()->where('shared_with_user_id', $userId)->exists();
    }

    public function canUserEdit($userId)
    {
        return $this->user_id === $userId ||
               $this->shares()->where('shared_with_user_id', $userId)->where('can_edit', true)->exists();
    }

    public function canUserView($userId)
    {
        return $this->user_id === $userId ||
               $this->is_public ||
               $this->isSharedWith($userId);
    }
}
