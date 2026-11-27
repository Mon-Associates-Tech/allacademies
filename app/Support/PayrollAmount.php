<?php

namespace App\Support;

use InvalidArgumentException;

class PayrollAmount
{
    public static function toCents(string|int|float|null $amount): int
    {
        $amount = trim((string) ($amount ?? '0'));

        if (! preg_match('/^(?:(\d+)(?:\.(\d{0,2}))?|\.(\d{1,2}))$/D', $amount, $matches)) {
            throw new InvalidArgumentException('Payroll amounts must be non-negative and have at most two decimal places.');
        }

        $whole = $matches[1] ?? '0';
        $fraction = $matches[2] ?? ($matches[3] ?? '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    public static function netPayInCents(string|int|float|null $baseSalary, string|int|float|null $allowances, string|int|float|null $deductions): int
    {
        return self::toCents($baseSalary) + self::toCents($allowances) - self::toCents($deductions);
    }

    public static function formatCents(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $absoluteCents = abs($cents);

        return $sign.intdiv($absoluteCents, 100).'.'.str_pad((string) ($absoluteCents % 100), 2, '0', STR_PAD_LEFT);
    }
}
