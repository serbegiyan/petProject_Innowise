<?php

namespace Tests\Feature\Services;

use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_converts_byn_amount_using_unit_rate(): void
    {
        ExchangeRate::factory()->byn()->create(['id' => 1]);
        ExchangeRate::factory()->create(['id' => 2, 'name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        session(['currency_id' => 2]);

        $service = app(CurrencyService::class);

        $this->assertSame(33.33, $service->convertAmount(100));
        $this->assertSame('33.33 USD', $service->format(100));
    }

    public function test_it_respects_scale_when_converting(): void
    {
        ExchangeRate::factory()->byn()->create(['id' => 1]);
        ExchangeRate::factory()->create([
            'id' => 2,
            'name' => 'RUB',
            'rate' => 3.77,
            'scale' => 100,
        ]);

        session(['currency_id' => 2]);

        $service = app(CurrencyService::class);

        $this->assertSame(2652.52, $service->convertAmount(100));
    }

    public function test_it_returns_byn_formatted_amount_when_byn_selected(): void
    {
        ExchangeRate::factory()->byn()->create(['id' => 1]);
        ExchangeRate::factory()->create(['id' => 2, 'name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        session(['currency_id' => 1]);

        $service = app(CurrencyService::class);

        $this->assertSame(100.0, $service->convertAmount(100));
        $this->assertSame('100.00 BYN', $service->format(100));
    }

    public function test_it_ignores_byn_unit_rate_when_amount_is_already_in_byn(): void
    {
        ExchangeRate::factory()->create(['id' => 1, 'name' => 'BYN', 'rate' => 1.0, 'scale' => 2]);
        ExchangeRate::factory()->create(['id' => 2, 'name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        session(['currency_id' => 2]);

        $service = app(CurrencyService::class);

        $this->assertSame(33.33, $service->convertAmount(100));
    }
}
