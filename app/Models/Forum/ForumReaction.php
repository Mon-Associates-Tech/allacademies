<?php

namespace App\Models\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reactable_type',
        'reactable_id',
        'user_id',
        'type'
    ];

    public function reactable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
