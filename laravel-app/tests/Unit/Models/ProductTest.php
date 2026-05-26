<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_url_uses_public_storage_path_when_image_is_set(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/photo.jpg', 'image-data');

        $product = Product::factory()->create(['image' => 'products/photo.jpg']);

        $this->assertSame(
            Storage::disk('public')->url('products/photo.jpg'),
            $product->image_url
        );
        $this->assertArrayHasKey('image_url', $product->toArray());
    }

    public function test_image_url_uses_placeholder_when_image_is_missing(): void
    {
        $product = Product::factory()->create(['image' => null]);

        $this->assertStringContainsString('product-image.svg', $product->image_url);
    }

    public function test_image_url_uses_storage_for_default_seeded_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('product-image.png', 'png-data');

        $product = Product::factory()->create(['image' => 'product-image.png']);

        $this->assertSame(
            Storage::disk('public')->url('product-image.png'),
            $product->image_url
        );
    }

    public function test_image_url_uses_placeholder_for_external_urls(): void
    {
        $product = Product::factory()->create([
            'image' => 'https://via.placeholder.com/600x600.png',
        ]);

        $this->assertStringContainsString('product-image.svg', $product->image_url);
    }
}
