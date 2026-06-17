<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Services\StatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StatsServiceSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_stats_are_cached_until_model_changes(): void
    {
        Cache::flush();

        $service = app(StatsService::class);

        Product::factory()->count(2)->create();
        $this->assertSame(2, $service->getSidebarStats()['products_count']);
        $this->assertSame(2, $service->getSidebarStats()['products_count']);

        Product::factory()->create();

        $this->assertSame(3, $service->getSidebarStats()['products_count']);
    }
}
