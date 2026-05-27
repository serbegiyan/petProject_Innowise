<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Services\ProductRelationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRelationsServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест синхронизации категории
     */
    public function test_it_syncs_categories(): void
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $service = new ProductRelationsService;
        $service->syncCategories($product, $category->id);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    /**
     * Тест синхронизации услуг с пивот-данными (цена и срок)
     */
    public function test_it_syncs_services_with_pivot_data(): void
    {
        $product = Product::factory()->create();
        $service = Service::factory()->create();

        $services = [$service->id];
        $prices = [$service->id => 99];
        $terms = [$service->id => '3 дня'];

        $relations = new ProductRelationsService;
        $relations->syncServices($product, $services, $prices, $terms);

        $this->assertDatabaseHas('product_service', [
            'product_id' => $product->id,
            'service_id' => $service->id,
            'price' => 99,
            'term' => '3 дня',
        ]);
    }
}
