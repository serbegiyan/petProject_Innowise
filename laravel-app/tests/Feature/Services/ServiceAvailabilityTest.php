<?php

namespace Tests\Feature\Services;

use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_crash_when_database_table_is_missing()
    {
        $this->withoutExceptionHandling();
        Schema::dropIfExists('exchange_rates');

        $service = app(CurrencyService::class);
        $rates = $service->getCachedRates();

        $this->assertCount(0, $rates);
    }

    public function test_it_does_not_crash_app_initialization_without_database()
    {
        $this->withoutExceptionHandling();
        Schema::dropIfExists('exchange_rates');

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
    }
}
