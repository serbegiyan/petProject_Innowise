<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected BasketService $basketService) {}

    /**
     * Создать заказ из текущей корзины пользователя.
     *
     * @param  array<string, mixed>  $validatedData
     */
    public function createFromBasket(User $user, array $validatedData): Order
    {
        return DB::transaction(function () use ($user, $validatedData) {
            $checkoutData = $this->basketService->getCheckoutDetails();

            // 1. Создаем основной заказ.
            // Используем array_merge, так как ключи в $validatedData
            // уже совпадают с твоим $fillable (customer_name, customer_phone и т.д.)
            $order = Order::create(
                array_merge($validatedData, [
                    'user_id' => $user->id,
                    'total_price' => $checkoutData['totalAmount'],
                    'status' => 'pending',
                ]),
            );

            // 2. Копируем товары из корзины в order_items
            foreach ($checkoutData['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                    'services' => $item['services'],
                ]);
            }

            // 3. Очищаем корзину
            $user->baskets()->delete();

            return $order;
        });
    }
}
