<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'type',
        'value',
        'label',
        'description',
        'group',
        'options',
        'required',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean'
    ];

    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => (bool)$value,
            'number' => is_numeric($value) ? (float)$value : $value,
            'json' => json_decode($value, true),
            'image', 'pdf' => $value ? Storage::url($value) : null,
            default => $value,
        };
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->type) {
            'json' => is_array($value) ? json_encode($value) : $value,
            'boolean' => $value ? '1' : '0',
            default => $value,
        };
    }

    public function getRawValueAttribute()
    {
        return $this->attributes['value'];
    }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        }
        return $setting;
    }

    public static function getGrouped()
    {
        return static::orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->map(function ($group) {
                return $group->keyBy('key');
            });
    }
}
