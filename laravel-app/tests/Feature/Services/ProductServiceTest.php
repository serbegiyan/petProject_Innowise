<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\Service;
use App\Services\ProductService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->productService = $this->app->make(ProductService::class);
    }

    public function test_it_creates_product_with_relations_and_image(): void
    {
        $category = Category::factory()->create();
        $service = Service::factory()->create();

        $data = [
            'name' => 'Test Product',
            'price' => 100,
        ];

        $relationData = [
            'category_ids' => [$category->id],
            'services' => [$service->id],
            'service_prices' => [$service->id => 50],
            'service_terms' => [$service->id => '2 дня'],
        ];

        $product = $this->productService->create(
            $data,
            UploadedFile::fake()->image('test.jpg'),
            $relationData
        );

        $this->assertModelExists($product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertTrue($product->categories->contains($category));

        $this->assertDatabaseHas('product_service', [
            'product_id' => $product->id,
            'service_id' => $service->id,
            'price' => 50,
            'term' => '2 дня',
        ]);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists($product->image);
    }
}
