<?php

namespace Tests\Feature\Order;

use App\Models\Basket;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_order_with_comment()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        ExchangeRate::factory()->create(['id' => 1]);

        Basket::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $orderData = [
            'customer_name' => 'Ivan Ivanov',
            'customer_address' => 'Test Street 1',
            'customer_phone' => '1234567890',
            'customer_email' => 'ivan@example.com',
            'payment_method' => 'cash',
            'comment' => 'Позвоните мне за час до доставки', // Тот самый комментарий
        ];

        $response = $this->actingAs($user)->post(route('order.store'), $orderData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ivan Ivanov',
            'comment' => 'Позвоните мне за час до доставки',
            'user_id' => $user->id,
            'total_price' => '100.00',
        ]);
    }

    public function test_order_item_price_includes_services_and_matches_total(): void
    {
        $user = User::factory()->create();
        ExchangeRate::factory()->create(['id' => 1]);

        $product = Product::factory()->create(['price' => 100]);
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => 50, 'term' => '7 days']);

        Basket::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'services' => [$service->id],
        ]);

        $response = $this->actingAs($user)->post(route('order.store'), [
            'customer_name' => 'Ivan Ivanov',
            'customer_address' => 'Test Street 1',
            'customer_phone' => '1234567890',
            'customer_email' => 'ivan@example.com',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => '300.00',
        ]);

        $order = Order::where('user_id', $user->id)->first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'price' => '150.00',
        ]);

        $orderItem = OrderItem::where('order_id', $order->id)->first();

        $this->assertSame('150.00', $orderItem->price);
        $this->assertEquals(2, $orderItem->quantity);

        $savedServices = is_string($orderItem->services)
            ? json_decode($orderItem->services, true)
            : $orderItem->services;

        $this->assertCount(1, $savedServices);
        $this->assertEquals($service->id, $savedServices[0]['id']);
        $this->assertEquals($service->name, $savedServices[0]['name']);
        $this->assertEquals('50.00', $savedServices[0]['price']);
    }

    public function test_it_rejects_order_when_basket_is_empty(): void
    {
        $user = User::factory()->create();
        ExchangeRate::factory()->create(['id' => 1]);

        $response = $this->actingAs($user)->post(route('order.store'), [
            'customer_name' => 'Ivan Ivanov',
            'customer_address' => 'Test Street 1',
            'customer_phone' => '1234567890',
            'customer_email' => 'ivan@example.com',
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors('basket');
        $this->assertDatabaseCount('orders', 0);
    }
}
