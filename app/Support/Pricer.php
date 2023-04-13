<?php

namespace App\Support;

use Brick\Money\Money;
use App\Enums\SubscriptionPackage;

class Pricer
{
    public static function calculate(SubscriptionPackage $package, int $duration, int $subjects, int $beneficiaries): Money
    {
        $unit = static::getUnitPrice($package, $duration);

        $money = Money::of($unit, 'GHS');

        if (SubscriptionPackage::INSTITUTION_FULL === $package) {
            $money = $money->multipliedBy($beneficiaries);
        }

        $money = $money->multipliedBy($subjects);

        return $money;
    }

    public static function getUnitPrice(SubscriptionPackage $package, int $duration): int
    {
        if (SubscriptionPackage::INSTITUTION_FULL === $package && 12 === $duration) {
            return 6;
        }

        if (SubscriptionPackage::INSTITUTION_FULL === $package && 6 === $duration) {
            return 9;
        }

        if (SubscriptionPackage::INSTITUTION_FULL === $package) {
            return 15;
        }

        if (12 === $duration) {
            return 8;
        }

        if (6 === $duration) {
            return 12;
        }

        return 20;
    }
}
