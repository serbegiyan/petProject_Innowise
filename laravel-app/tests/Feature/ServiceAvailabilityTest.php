<?php

namespace Tests\Feature;

use App\Services\StatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_crash_when_database_table_is_missing()
    {
        $this->withoutExceptionHandling();
        Schema::dropIfExists('exchange_rates');

        $service = app(StatsService::class);
        $rates = $service->getExchangeRates();

        $this->assertInstanceOf(Collection::class, $rates);
        $this->assertTrue($rates->isEmpty());
    }

    public function test_it_does_not_crash_app_initialization_without_database()
    {
        $this->withoutExceptionHandling();
        Schema::dropIfExists('exchange_rates');

        $response = $this->get('/catalog');

        $response->assertStatus(200);
    }
}
