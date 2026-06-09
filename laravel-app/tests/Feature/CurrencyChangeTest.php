<?php

namespace Tests\Feature;

use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_selected_currency_in_session(): void
    {
        ExchangeRate::factory()->byn()->create(['id' => 1]);
        $usd = ExchangeRate::factory()->create(['id' => 2, 'name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        $response = $this->postJson(route('currency.change'), ['id' => $usd->id]);

        $response->assertOk();
        $this->assertSame($usd->id, session('currency_id'));
    }

    public function test_inertia_request_redirects_back(): void
    {
        $usd = ExchangeRate::factory()->create(['id' => 2, 'name' => 'USD', 'rate' => 3.0, 'scale' => 1]);

        $response = $this->post(route('currency.change'), ['id' => $usd->id], [
            'X-Inertia' => 'true',
        ]);

        $response->assertRedirect();
        $this->assertSame($usd->id, session('currency_id'));
    }
}
