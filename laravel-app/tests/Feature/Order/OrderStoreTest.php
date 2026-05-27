<?php

namespace Tests\Feature\Order;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prevents_creating_an_order_with_an_empty_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('order.store'), [
            'customer_name' => 'John Doe',
            'customer_phone' => '+375291112233',
            'customer_email' => 'john@doe.com',
            'customer_address' => '123 Main St, Anytown, USA',
            'items' => [],
        ]);

        $this->assertDatabaseCount('orders', 0);

        $response->assertSessionHasErrors(['basket' => 'Корзина пуста.']);

        $response->assertRedirect();
    }
}
