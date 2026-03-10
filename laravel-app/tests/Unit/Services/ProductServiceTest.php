<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Service;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_product_with_relations_and_image()
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $service = Service::factory()->create();

        $data = [
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'Some text',
        ];

        // 2. Request для файлов и связей
        $request = request()->merge([
            'category_id' => [$category->id],
            'services' => [$service->id],
            'service_prices' => [$service->id => 50],
            'service_terms' => [$service->id => '2 дня'],
        ]);

        $request->files->set('image', UploadedFile::fake()->image('test.jpg'));

        // 3. Вызов сервиса
        $serviceLayer = app(ProductService::class);
        $product = $serviceLayer->create($data, $request);

        // 4. Проверки
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product',
        ]);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('product_service', [
            'product_id' => $product->id,
            'service_id' => $service->id,
            'price' => 50,
            'term' => '2 дня',
        ]);

        Storage::disk('public')->assertExists($product->image);
    }
}
