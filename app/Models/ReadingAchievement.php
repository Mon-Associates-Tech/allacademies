<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'description',
        'criteria',
        'awarded_at'
    ];

    protected $casts = [
        'criteria' => 'array',
        'awarded_at' => 'datetime'
    ];

    /**
     * Get the user that owns the achievement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Award a new achievement to a user
     */
    public static function award($userId, $type, $name, $description, $criteria = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'name' => $name,
            'description' => $description,
            'criteria' => $criteria,
            'awarded_at' => now()
        ]);
    }
}
