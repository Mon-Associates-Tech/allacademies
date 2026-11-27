<?php

namespace Tests\Unit;

use App\Support\PayrollAmount;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PayrollAmountTest extends TestCase
{
    public function test_it_calculates_net_pay_using_integer_minor_units(): void
    {
        $this->assertSame(265025, PayrollAmount::netPayInCents('2500.00', '250.50', '100.25'));
        $this->assertSame('2650.25', PayrollAmount::formatCents(265025));
    }

    public function test_it_rejects_negative_or_over_precision_amounts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PayrollAmount::toCents('-1.00');
    }

    public function test_it_rejects_amounts_with_more_than_two_decimal_places(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PayrollAmount::toCents('1.234');
    }
}
