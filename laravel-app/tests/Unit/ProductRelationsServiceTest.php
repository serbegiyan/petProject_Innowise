<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Service;
use App\Services\ProductRelationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRelationsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_categories()
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $request = request()->merge([
            'category_id' => [$category->id],
        ]);

        $service = new ProductRelationsService();
        $service->syncCategories($product, $request);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_it_syncs_services_with_pivot_data()
    {
        $product = Product::factory()->create();
        $service = Service::factory()->create();

        $request = request()->merge([
            'services' => [$service->id],
            'service_prices' => [$service->id => 99],
            'service_terms' => [$service->id => '3 дня'],
        ]);

        $relations = new ProductRelationsService();
        $relations->syncServices($product, $request);

        $this->assertDatabaseHas('product_service', [
            'product_id' => $product->id,
            'service_id' => $service->id,
            'price' => 99,
            'term' => '3 дня',
        ]);
    }
}
