<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenAiTokenUsageLog extends Model
{
    use HasFactory;

    protected $table = 'openai_token_usage_logs';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'subscription_cycle_id',
        'model',
        'prompt_tokens', // tracks input_tokens
        'completion_tokens', // tracks output_tokens
        'total_tokens', // sum of input_tokens and output_tokens
        'request_type',
        'context',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserTokenSubscription::class, 'subscription_id');
    }

    public function subscriptionCycle(): BelongsTo
    {
        return $this->belongsTo(SubscriptionCycle::class, 'subscription_cycle_id');
    }
}
