<?php

namespace Tests\Unit\Support;

use App\Support\Money;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    #[Test]
    public function it_adds_amounts_without_float_drift(): void
    {
        $this->assertSame('100.00', Money::add('99.99', '0.01'));
    }

    #[Test]
    public function it_multiplies_with_two_decimal_precision(): void
    {
        $this->assertSame('20.00', Money::mul('10.00', 2));
        $this->assertSame('30.00', Money::mul('10.00', '3'));
    }

    #[Test]
    public function it_sums_iterable_amounts(): void
    {
        $this->assertSame('150.00', Money::sum(['100.00', '49.99', '0.01']));
    }

    #[Test]
    public function it_normalizes_numeric_strings(): void
    {
        $this->assertSame('10.50', Money::round('10.5'));
        $this->assertSame('10.50', Money::round(10.5));
    }
}
