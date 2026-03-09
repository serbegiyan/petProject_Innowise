<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $response->assertInertia(fn($page) => $page->component('Catalog/Index')->where('filters.search', 'iphone')->has('products.data', 1)->where('products.data.0.id', $iphone->id));
    }
}
