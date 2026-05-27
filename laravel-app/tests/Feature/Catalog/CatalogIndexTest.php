<?php

namespace Tests\Feature\Catalog;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_filters_products_by_search()
    {
        // Создаём товары
        $iphone = Product::factory()->create(['name' => 'iPhone 15']);
        Product::factory()->create(['name' => 'Samsung Galaxy']);

        // Делаем запрос с фильтром
        $response = $this->get('/catalog?search=iphone');

        // Проверяем ответ
        $response->assertStatus(200);

        $response->assertInertia(fn ($page) => $page
            ->component('Catalog/Index')
            ->where('filters.search', 'iphone')
            ->has('products.data', 1)
            ->where('products.data.0.id', $iphone->id)
            ->missing('products.data.0.services')
        );
    }

    public function test_catalog_index_includes_pagination_links_at_root_level(): void
    {
        Product::factory()->count(13)->create();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Catalog/Index')
                ->has('products.data', 12)
                ->has('products.meta.links', 4)
                ->has('products.links')
                ->where('products.meta.links.1.url', fn (string $url) => str_contains($url, '/catalog'))
            );
    }
}
