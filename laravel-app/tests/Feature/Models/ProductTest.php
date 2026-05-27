<?php

namespace Tests\Feature\Models;

use App\Models\Product;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяем, что загруженное изображение возвращает корректный URL с диска public.
     */
    public function test_image_url_uses_public_storage_path_when_image_is_set(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/photo.jpg', 'image-data');

        $product = Product::factory()->create(['image' => 'products/photo.jpg']);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $this->assertSame(
            $disk->url('products/photo.jpg'),
            $product->image_url
        );
        $this->assertArrayHasKey('image_url', $product->toArray());
    }

    /**
     * Проверяем, что при отсутствии картинки (null) возвращается корректная заглушка.
     */
    public function test_image_url_uses_placeholder_when_image_is_missing(): void
    {
        $product = Product::factory()->create(['image' => null]);

        $this->assertStringContainsString('product-image.png', $product->image_url);
    }

    /**
     * Проверяем резервный возврат заглушки, если запись в базе есть, но файл физически удален.
     */
    public function test_image_url_uses_placeholder_if_file_is_missing_on_disk(): void
    {
        Storage::fake('public');

        // Запись в базе есть, но на диске файла НЕТ
        $product = Product::factory()->create(['image' => 'products/lost-file.jpg']);

        $this->assertStringContainsString('product-image.png', $product->image_url);
    }

    /**
     * Проверяем внешние ссылки (Faker).
     */
    public function test_image_url_returns_external_url_directly(): void
    {
        $externalUrl = 'https://via.placeholder.com/600x600.png';

        $product = Product::factory()->create([
            'image' => $externalUrl,
        ]);

        $this->assertSame($externalUrl, $product->image_url);
    }
}
