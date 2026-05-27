<?php

namespace Tests\Feature\Order;

use App\Models\ExchangeRate;
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
        // 1. Подготовка данных
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        // Создаем валюту, так как она нужна для глобального Share
        ExchangeRate::factory()->create(['id' => 1]);

        // Имитируем товар в корзине (BasketService берет данные отсюда)
        $user->baskets()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [],
        ]);

        $orderData = [
            'customer_name' => 'Ivan Ivanov',
            'customer_address' => 'Test Street 1',
            'customer_phone' => '1234567890',
            'customer_email' => 'ivan@example.com',
            'payment_method' => 'cash',
            'comment' => 'Позвоните мне за час до доставки', // Тот самый комментарий
        ];

        // 2. Действие
        $response = $this->actingAs($user)->post(route('order.store'), $orderData);

        // 3. Проверки
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ivan Ivanov',
            'comment' => 'Позвоните мне за час до доставки',
            'user_id' => $user->id,
            'total_price' => 100,
        ]);
    }

    public function test_order_item_price_includes_services_and_matches_total(): void
    {
        $user = User::factory()->create();
        ExchangeRate::factory()->create(['id' => 1]);

        $product = Product::factory()->create(['price' => 100]);
        $service = Service::factory()->create();
        $product->services()->attach($service->id, ['price' => 50, 'term' => '7 days']);

        $user->baskets()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'services' => [['id' => $service->id, 'name' => $service->name, 'price' => 50]],
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
            'total_price' => 300,
        ]);

        $orderItem = OrderItem::first();
        $this->assertEquals(150.0, (float) $orderItem->price);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(50.0, $orderItem->services[0]['price']);
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
