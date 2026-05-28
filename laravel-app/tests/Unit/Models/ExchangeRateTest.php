<?php

namespace Tests\Unit\Models;

use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_converts_amount_from_byn_to_foreign_currency(): void
    {
        $byn = ExchangeRate::factory()->byn()->create();
        ExchangeRate::factory()->create(['name' => 'USD', 'rate' => 2.0, 'scale' => 1]);

        $this->assertSame(50.0, $byn->convertAmountTo('USD', 100.0));
        $this->assertSame(0.0, $byn->convertAmountTo('XXX', 100.0));
    }

    #[Test]
    public function it_returns_same_amount_when_target_currency_matches(): void
    {
        $byn = ExchangeRate::factory()->byn()->create();

        $this->assertSame(42.5, $byn->convertAmountTo('BYN', 42.5));
    }
}
