<?php

namespace Tests\Feature\Models;

use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_converts_amount_from_byn_to_foreign_currency(): void
    {
        $byn = ExchangeRate::factory()->byn()->create();
        $usd = ExchangeRate::factory()->create(['name' => 'USD', 'rate' => 2.0, 'scale' => 1]);

        $this->assertSame(50.0, ExchangeRate::convertFromByn(100.0, $usd));
        $this->assertSame(50.0, $byn->convertAmountTo('USD', 100.0));
        $this->assertSame(0.0, $byn->convertAmountTo('XXX', 100.0));
    }

    public function test_convert_from_byn_ignores_byn_unit_rate(): void
    {
        $usd = ExchangeRate::factory()->create(['name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        $this->assertSame(33.33, ExchangeRate::convertFromByn(100.0, $usd));
    }

    public function test_it_returns_same_amount_when_target_currency_matches(): void
    {
        $byn = ExchangeRate::factory()->byn()->create();

        $this->assertSame(42.5, $byn->convertAmountTo('BYN', 42.5));
    }
}
