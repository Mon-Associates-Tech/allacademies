<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'meta', 'team_id'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
