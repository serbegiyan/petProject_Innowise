<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageServiceTest extends TestCase
{
    public function test_it_uploads_new_image(): void
    {
        Storage::fake('public');

        $imageFile = UploadedFile::fake()->image('new.jpg');

        $service = new ProductImageService;
        $path = $service->handle($imageFile);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $this->assertNotNull($path);
        $storage->assertExists($path);
    }

    public function test_it_stores_new_image_without_deleting_old_in_handle(): void
    {
        Storage::fake('public');

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $product = Product::factory()->create([
            'image' => 'products/old.jpg',
        ]);
        $storage->put('products/old.jpg', 'old content');

        $newImageFile = UploadedFile::fake()->image('new.jpg');

        $service = new ProductImageService;
        $path = $service->handle($newImageFile, $product);

        $storage->assertExists('products/old.jpg');
        $storage->assertExists($path);
    }

    public function test_it_deletes_image_when_exists(): void
    {
        Storage::fake('public');

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->put('products/old.jpg', 'old content');

        $service = new ProductImageService;
        $service->deleteIfExists('products/old.jpg');

        $storage->assertMissing('products/old.jpg');
    }
}
