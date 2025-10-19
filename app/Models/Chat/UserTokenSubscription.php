<?php

namespace App\Models\Chat;

use App\Models\Payment;
use App\Models\User;
use App\Support\TokenSubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserTokenSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_token_subscriptions';

    protected $fillable = [
        'user_id',
        'package_id',
        'reference',
        'tokens_purchased',
        'tokens_used',
        'tokens_remaining',
        'purchased_at',
        'expires_at',
        'activated_at',
        'deactivated_at',
        'status',
        'action_type',
        'replaced_by_id',
    ];

    protected $casts = [
        'tokens_purchased' => 'integer',
        'tokens_used' => 'integer',
        'tokens_remaining' => 'integer',
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'status' => TokenSubscriptionStatus::class,
    ];

    protected $appends = [
        'usage_percentage',
        'remaining_percentage',
    ];

    // Define valid status values
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_DEPLETED = 'depleted';
    public const STATUS_REPLACED = 'replaced';

    public static array $validStatuses = [
        self::STATUS_ACTIVE,
        self::STATUS_PENDING,
        self::STATUS_EXPIRED,
        self::STATUS_DEPLETED,
        self::STATUS_REPLACED
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->reference)) {
                $subscription->reference = 'TOKEN-' . strtoupper(Str::random(10)) . '-' . time();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(OpenAiTokenPackage::class, 'package_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(OpenAiTokenUsageLog::class, 'subscription_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'token_subscription_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(UserTokenSubscription::class, 'replaced_by_id');
    }

    public function replaces(): HasMany
    {
        return $this->hasMany(UserTokenSubscription::class, 'replaced_by_id');
    }

    /**
     * Check if subscription has enough tokens
     */
    public function hasTokens(int $requiredTokens = 1): bool
    {
        return $this->status === 'active' && $this->tokens_remaining >= $requiredTokens;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Deduct tokens from subscription
     */
    public function deductTokens(int $tokens): bool
    {
        if (!$this->hasTokens($tokens)) {
            return false;
        }

        $this->tokens_used += $tokens;
        $this->tokens_remaining = $this->tokens_purchased - $this->tokens_used;

        if ($this->tokens_remaining <= 0) {
            $this->status = 'depleted';
            $this->deactivated_at = now();
        }

        return $this->save();
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->tokens_purchased == 0) {
            return 0;
        }

        return round(($this->tokens_used / $this->tokens_purchased) * 100, 2);
    }

    /**
     * Get remaining percentage
     */
    public function getRemainingPercentageAttribute(): float
    {
        return 100 - $this->usage_percentage;
    }

    /**
     * Check if nearing depletion (below 10%)
     */
    public function isNearingDepletion(): bool
    {
        return $this->usage_percentage >= 90;
    }

    /**
     * Deactivate this subscription
     */
    public function deactivate(string $reason = 'replaced'): void
    {
        $validReasons = ['replaced', 'expired', 'depleted'];

        if (in_array($reason, $validReasons)) {
            $this->status = TokenSubscriptionStatus::from($reason);
        } else {
            $this->status = TokenSubscriptionStatus::REPLACED;
        }

        $this->deactivated_at = now();
        $this->save();
    }

    /**
     * Activate this subscription
     */
    public function activate(): void
    {
        $this->status = TokenSubscriptionStatus::ACTIVE;
        $this->activated_at = now();
        $this->purchased_at = $this->purchased_at ?? now();
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', TokenSubscriptionStatus::ACTIVE->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', TokenSubscriptionStatus::PENDING->value);
    }

    public function scopeHistory($query)
    {
        return $query->whereIn('status', [
            TokenSubscriptionStatus::EXPIRED->value,
            TokenSubscriptionStatus::DEPLETED->value,
            TokenSubscriptionStatus::REPLACED->value,
        ]);
    }
}
