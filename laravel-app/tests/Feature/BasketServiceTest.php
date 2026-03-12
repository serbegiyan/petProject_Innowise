<?php

namespace Tests\Feature\Services;

use App\Models\Basket;
use App\Models\Product;
use App\Models\User;
use App\Services\BasketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasketServiceTest extends TestCase
{
    use RefreshDatabase; // Очищаем базу перед каждым тестом

    private BasketService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BasketService;
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_add_product_to_basket_for_authenticated_user()
    {
        // 1. Авторизуем пользователя (чтобы сработал хук $this->userId)
        $this->actingAs($this->user);
        $product = Product::factory()->create(['price' => 100]);

        $data = [
            'product_id' => $product->id,
            'services' => [],
        ];

        // 2. Действие
        $this->service->addToBasket($data);

        // 3. Проверка в базе
        $this->assertDatabaseHas('baskets', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function it_throws_exception_if_user_is_not_authenticated()
    {
        // НЕ вызываем actingAs() — хук должен выбросить Exception
        $product = Product::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $this->service->addToBasket(['product_id' => $product->id]);
    }

    /** @test */
    public function it_increments_quantity_if_product_and_services_are_same()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create();

        // Создаем существующую запись в корзине
        Basket::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'services' => [],
        ]);

        // Пытаемся добавить такой же товар
        $this->service->addToBasket(['product_id' => $product->id, 'services' => []]);

        // Проверяем, что количество стало 2, а не создалась новая запись
        $this->assertEquals(2, Basket::where('user_id', $this->user->id)->first()->quantity);
        $this->assertEquals(1, Basket::count());
    }
}
