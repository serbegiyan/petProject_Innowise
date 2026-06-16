<?php

namespace Tests\Feature\Catalog;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_show_includes_product_services_with_pivot(): void
    {
        $product = Product::factory()->create();
        $service = Service::factory()->create(['name' => 'Гарантия']);
        $product->services()->attach($service->id, [
            'price' => 25.5,
            'term' => '12 months',
        ]);

        $response = $this->get(route('catalog.show', $product));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Catalog/Show')
            ->has('product.services', 1)
            ->where('product.services.0.id', $service->id)
            ->where('product.services.0.name', 'Гарантия')
            ->where('product.services.0.pivot.price', fn ($price) => in_array($price, [25.5, '25.50'], true))
            ->where('product.services.0.pivot.term', '12 months')
        );
    }

    public function test_catalog_index_does_not_include_services_in_product_payload(): void
    {
        Product::factory()
            ->hasAttached(Service::factory(), ['price' => 10, 'term' => '1 day'])
            ->create(['name' => 'Test Product']);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Catalog/Index')
            ->has('products.data', 1)
            ->missing('products.data.0.services')
        );
    }
}
