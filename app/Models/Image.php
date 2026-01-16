<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder search(string $words)
 **/
class Image extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'description',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public static function scopeSearch(Builder $query, string $words)
    {
        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $words = preg_split('/\s+/', $words, -1, PREG_SPLIT_NO_EMPTY);

        return $query->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->orWhere('description', 'like', "%{$word}%")
                    ->orWhereFullText('description', $word)
                    ->orWhereRaw('lower(tags) like lower(?)', ["%{$word}%"])
                    ->orWhere('description', 'sounds like', $word)
                    ->orWhere('tags', 'sounds like', $word);
            }
        });
    }
}
