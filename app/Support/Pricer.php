<?php

namespace App\Support;

use Brick\Money\Money;
use App\Enums\SubscriptionPackage;

class Pricer
{
    public static function calculate(SubscriptionPackage $package, int $duration, int $subjects, int $beneficiaries): Money
    {
        info(json_encode([$package, $duration, $subjects, $beneficiaries]));
        $unit = SubscriptionPackage::INSTITUTION_FULL === $package ? '1': '2';

        $money = Money::of($unit, 'GHS');

        $money = $money->multipliedBy($duration);

        if (SubscriptionPackage::INSTITUTION_FULL === $package) {
            $money = $money->multipliedBy($beneficiaries);
        }

        $money = $money->multipliedBy($subjects);

        return $money;
    }
}
