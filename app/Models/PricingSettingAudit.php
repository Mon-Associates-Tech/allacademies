<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingSettingAudit extends Model
{
    protected $fillable = [
        'pricing_setting_id',
        'key',
        'old_value',
        'new_value',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_value' => 'decimal:2',
        'new_value' => 'decimal:2',
    ];

    public function pricingSetting(): BelongsTo
    {
        return $this->belongsTo(PricingSetting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
