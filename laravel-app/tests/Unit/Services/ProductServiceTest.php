<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Service;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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

        $request = request()->merge([
            'category_id' => [$category->id],
            'services' => [$service->id],
            'service_prices' => [$service->id => 50],
            'service_terms' => [$service->id => '2 дня'],
        ]);

        $request->files->set('image', UploadedFile::fake()->image('test.jpg'));

        $serviceLayer = app(ProductService::class);
        $product = $serviceLayer->create($data, $request);

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
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists($product->image);
    }
}
