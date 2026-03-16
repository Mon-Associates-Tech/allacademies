<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PricingSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    private static ?array $cache = null;

    public static function getValue(string $key): ?float
    {
        if (self::$cache === null) {
            if (! Schema::hasTable('pricing_settings')) {
                self::$cache = [];
            } else {
                self::$cache = self::query()->pluck('value', 'key')->toArray();
            }
        }

        $value = self::$cache[$key] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }

    public static function flushCache(): void
    {
        self::$cache = null;
    }

    public static function brandingPricing(): array
    {
        $pricing = config('branding_pricing');

        if (! Schema::hasTable('pricing_settings')) {
            return $pricing;
        }

        $values = self::query()->pluck('value', 'key')->toArray();

        foreach (self::brandingKeyMap() as $key => $path) {
            if (isset($values[$key]) && is_numeric($values[$key])) {
                data_set($pricing, $path, (float) $values[$key]);
            }
        }

        return $pricing;
    }

    private static function brandingKeyMap(): array
    {
        return [
            'basic.individual.quarter' => 'plans.basic.options.quarterly.price',
            'basic.individual.half' => 'plans.basic.options.biannual.price',
            'basic.individual.year' => 'plans.basic.options.annual.price',

            'senior.individual.quarter' => 'plans.secondary.options.quarterly.price',
            'senior.individual.half' => 'plans.secondary.options.biannual.price',
            'senior.individual.year' => 'plans.secondary.options.annual.price',

            'basic.institution.quarter' => 'plans.institutional.options.quarterly.tiers.basic.price',
            'basic.institution.year' => 'plans.institutional.options.basic_annual.price',

            'senior.institution.quarter' => 'plans.institutional.options.quarterly.tiers.secondary.price',
            'senior.institution.year' => 'plans.institutional.options.secondary_annual.price',
        ];
    }
}
