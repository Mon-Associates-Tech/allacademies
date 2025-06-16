<?php

namespace App\Support;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use App\Enums\SubscriptionPackage;

class Pricer
{
    /**
     * @throws RoundingNecessaryException
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public static function calculate(SubscriptionPackage $package, int $duration, int $subjects, int $beneficiaries, ?string $tag): Money
    {
//        $unit = static::getUnitPrice($package, $duration);

        $unit = SubscriptionCalculator::unitSubscriptionPrice($package,  $duration, $tag);

        $money = Money::of($unit, 'GHS');

        if (SubscriptionPackage::INSTITUTION_FULL === $package) {
            $money = $money->multipliedBy($beneficiaries);
        }

        if(SubscriptionPackage::INDIVIDUAL_FULL === $package) {
            $money = $money->multipliedBy($subjects);
        }

        return $money;
    }

    public static function getUnitPrice(SubscriptionPackage $package, int $duration): int
    {
        if (SubscriptionPackage::INSTITUTION_FULL === $package && 12 === $duration) {
            return 15;
        }

        if (SubscriptionPackage::INSTITUTION_FULL === $package && 6 === $duration) {
            return 9;
        }

        if (SubscriptionPackage::INSTITUTION_FULL === $package) {
            return 6;
        }

        if (12 === $duration) {
            return 30;
        }

        if (6 === $duration) {
            return 20;
        }

        return 15;
    }
}
