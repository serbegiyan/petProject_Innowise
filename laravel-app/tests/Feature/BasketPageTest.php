<?php

namespace Tests\Feature;

use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BasketPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_basket_page_includes_product_image_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/photo.jpg', 'image-data');

        ExchangeRate::factory()->create(['id' => 1]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['image' => 'products/photo.jpg']);

        $user->baskets()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [],
        ]);

        $expectedUrl = Storage::disk('public')->url('products/photo.jpg');

        $this->actingAs($user)
            ->get(route('basket.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Basket/Index')
                ->has('items', 1)
                ->where('items.0.product.image_url', $expectedUrl)
            );
    }
}
