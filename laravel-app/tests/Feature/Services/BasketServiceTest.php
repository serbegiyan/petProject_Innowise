<?php

namespace Tests\Feature\Services;

use App\Models\Basket;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\BasketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasketServiceTest extends TestCase
{
    use RefreshDatabase;

    private BasketService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BasketService;
        $this->user = User::factory()->create();
    }

    public function test_it_can_add_product_to_basket_for_authenticated_user()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['price' => 100]);

        $data = [
            'product_id' => $product->id,
            'services' => [],
        ];

        $this->service->addToBasket($data);

        $this->assertDatabaseHas('baskets', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services_key' => '[]',
        ]);
    }

    public function test_it_throws_exception_if_user_is_not_authenticated()
    {
        $product = Product::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $this->service->addToBasket(['product_id' => $product->id]);
    }

    public function test_it_increments_quantity_if_product_and_services_are_same()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create();

        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [],
        ]);

        $this->service->addToBasket(['product_id' => $product->id, 'services' => []]);

        $this->assertEquals(2, Basket::where('user_id', $this->user->id)->first()->quantity);
        $this->assertEquals(1, Basket::count());
    }

    public function test_checkout_total_includes_service_pivot_prices(): void
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create(['price' => 100]);
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => 50, 'term' => '7 days']);

        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [$service->id],
        ]);

        $details = $this->service->getCheckoutDetails();

        $this->assertSame('150.00', $details['totalAmount']);
        $this->assertSame('150.00', $details['items']->first()['item_total']);
    }

    public function test_checkout_total_supports_legacy_service_object_format(): void
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create(['price' => 100]);
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => 50, 'term' => '7 days']);

        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [['id' => $service->id, 'name' => $service->name, 'price' => 50]],
        ]);

        $details = $this->service->getCheckoutDetails();

        $this->assertSame('150.00', $details['totalAmount']);
    }

    public function test_checkout_total_avoids_float_rounding_errors(): void
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create(['price' => '99.99']);
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => '0.01', 'term' => '7 days']);

        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [$service->id],
        ]);

        $details = $this->service->getCheckoutDetails();

        $this->assertSame('100.00', $details['totalAmount']);
        $this->assertSame('100.00', $details['items']->first()['single_price']);
    }

    public function test_it_sets_services_key_when_adding_with_services(): void
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create();
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => 10, 'term' => '7 days']);

        $this->service->addToBasket([
            'product_id' => $product->id,
            'services' => [['id' => $service->id]],
        ]);

        $this->assertDatabaseHas('baskets', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'services_key' => (string) $service->id,
        ]);
    }

    public function test_services_key_normalizes_legacy_object_format(): void
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create();
        $service = Service::factory()->create();

        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [['id' => $service->id, 'name' => $service->name, 'price' => 10]],
        ]);

        $this->service->addToBasket([
            'product_id' => $product->id,
            'services' => [['id' => $service->id]],
        ]);

        $this->assertEquals(1, Basket::where('user_id', $this->user->id)->count());
        $this->assertEquals(2, Basket::where('user_id', $this->user->id)->first()->quantity);
    }
}
