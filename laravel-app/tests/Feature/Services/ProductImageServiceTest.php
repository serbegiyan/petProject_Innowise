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
    /**
     * Тест загрузки нового изображения, когда старого нет.
     */
    public function test_it_uploads_new_image(): void
    {
        Storage::fake('public');

        $imageFile = UploadedFile::fake()->image('new.jpg');

        $service = new ProductImageService;
        $path = $service->handle($imageFile);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $storage->assertExists($path);
    }

    /**
     * Тест автоматической замены старого изображения новым с удалением из хранилища.
     */
    public function test_it_replaces_old_image(): void
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

        $storage->assertMissing('products/old.jpg');
        $storage->assertExists($path);
    }
}
