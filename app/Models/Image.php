<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'tags' => 'array'
    ];

    public static function scopeSearch(Builder $query, string $userSearch)
    {
        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $userSearch, -1, PREG_SPLIT_NO_EMPTY);
        return static::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('description', 'like', "%{$value}%")
                ->orWhereFullText('description', $value)
                ->orWhereRaw('lower(tags) like lower(?)', ["%{$value}%"])
                ->orWhere('description', 'sounds like', $value)
                ->orWhere('tags', 'sounds like', $value);
            }
        });
    }
}
