<?php

namespace App\Models;

use App\Mail\BookShareResponded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class UserBookShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_book_id',
        'shared_by_user_id',
        'shared_to_user_id',
        'shared_to_email',
        'status',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function userBook(): BelongsTo
    {
        return $this->belongsTo(UserBook::class);
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function sharedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_to_user_id');
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $this->notifyOwner('accepted');
    }

    public function decline(): void
    {
        $this->update([
            'status' => 'declined',
        ]);

        $this->notifyOwner('declined');
    }

    public function notifyOwner($action): void
    {
        // Send email to the owner about the decision
        if ($this->sharedBy) {
            Mail::to($this->sharedBy->email)->send(
                new BookShareResponded($this, $action)
            );
        }
    }
}

