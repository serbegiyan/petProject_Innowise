<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageServiceTest extends TestCase
{
    public function test_it_uploads_new_image()
    {
        Storage::fake('public');

        $service = new ProductImageService;

        $request = request();
        $request->files->set('image', UploadedFile::fake()->image('new.jpg'));

        $path = $service->handle($request);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        $storage->assertExists($path);
    }

    public function test_it_replaces_old_image()
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image' => 'products/old.jpg',
        ]);

        Storage::disk('public')->put('products/old.jpg', 'old');

        $service = new ProductImageService;

        $request = request();
        $request->files->set('image', UploadedFile::fake()->image('new.jpg'));

        $path = $service->handle($request, $product);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertMissing('products/old.jpg');
        $storage->assertExists($path);
    }
}
