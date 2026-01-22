<?php

namespace App\Support;

use App\Enums\SubscriptionPackage;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

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

        $unit = SubscriptionCalculator::unitSubscriptionPrice($package, $duration, $tag);

        $money = Money::of($unit, 'GHS');

        if ($package === SubscriptionPackage::INSTITUTION_FULL) {
            $money = $money->multipliedBy($beneficiaries);
        }

        if ($package === SubscriptionPackage::INDIVIDUAL_FULL) {
            $money = $money->multipliedBy($subjects);
        }

        return $money;
    }

    public static function getUnitPrice(SubscriptionPackage $package, int $duration): int
    {
        if ($package === SubscriptionPackage::INSTITUTION_FULL && $duration === 12) {
            return 15;
        }

        if ($package === SubscriptionPackage::INSTITUTION_FULL && $duration === 6) {
            return 9;
        }

        if ($package === SubscriptionPackage::INSTITUTION_FULL) {
            return 6;
        }

        if ($duration === 12) {
            return 30;
        }

        if ($duration === 6) {
            return 20;
        }

        return 15;
    }
}
