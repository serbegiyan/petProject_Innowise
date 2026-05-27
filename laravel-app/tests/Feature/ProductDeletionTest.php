<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_it_keeps_relationships_and_image_during_soft_deletion(): void
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        // Явно сохраняем файл именно на диск public
        $file = UploadedFile::fake()->image('product.jpg');
        $path = $storage->putFile('products', $file);

        $product = Product::factory()->create(['image' => $path]);
        $category = Category::factory()->create();
        $service = Service::factory()->create();

        $product->categories()->attach($category);
        $product->services()->attach($service, ['price' => 100, 'term' => 2]);

        $product->delete();

        $this->assertSoftDeleted($product);

        // Проверяем существование файла строго на диске public
        $storage->assertExists($path);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_it_cleans_up_relationships_and_image_during_force_deletion(): void
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $file = UploadedFile::fake()->image('product.jpg');
        $path = $storage->putFile('products', $file);

        $product = Product::factory()->create(['image' => $path]);
        $category = Category::factory()->create();
        $service = Service::factory()->create();

        $product->categories()->attach($category);
        $product->services()->attach($service, ['price' => 100, 'term' => 2]);

        $product->forceDelete();

        $this->assertModelMissing($product);

        // Проверяем удаление файла строго на диске public
        $storage->assertMissing($path);

        $this->assertDatabaseMissing('category_product', ['product_id' => $product->id]);
        $this->assertDatabaseMissing('product_service', ['product_id' => $product->id]);
    }

    public function test_it_can_successfully_restore_a_soft_deleted_product(): void
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $product->categories()->attach($category);

        $product->delete();
        $product->restore();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
        $this->assertEquals(1, $product->categories()->count());
    }
}
