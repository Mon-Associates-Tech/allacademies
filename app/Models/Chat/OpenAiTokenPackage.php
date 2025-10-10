<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenAiTokenPackage extends Model
{
    use HasFactory;
    protected $table = 'openai_token_packages';

    protected $fillable = [
        'name',
        'model',
        'token_limit',
        'price',
        'description',
        'is_active',
        'is_free'
    ];

    protected $casts = [
        'token_limit' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    public function isFree(): bool
    {
        return $this->is_free || $this->price == 0;
    }
}
