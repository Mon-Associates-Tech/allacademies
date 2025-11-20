<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'shared_with_user_id',
        'can_edit'
    ];

    protected $casts = [
        'can_edit' => 'boolean'
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function sharer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
