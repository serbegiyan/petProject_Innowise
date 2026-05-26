<?php

namespace App\Services;

use App\DTO\OrderData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(protected BasketService $basketService) {}

    public function createFromBasket(User $user, OrderData $data): Order
    {
        $checkoutData = $this->basketService->getCheckoutDetails();

        if ($checkoutData['items']->isEmpty()) {
            throw ValidationException::withMessages([
                'basket' => 'Корзина пуста.',
            ]);
        }

        try {
            return DB::transaction(function () use ($user, $data, $checkoutData) {
                $order = $user->orders()->create([
                    'total_price' => $checkoutData['totalAmount'],
                    'customer_name' => $data->name,
                    'customer_address' => $data->address,
                    'customer_phone' => $data->phone,
                    'customer_email' => $data->email,
                    'status' => OrderStatus::PENDING->value,
                    'comment' => $data->comment,
                    'payment_method' => $data->paymentMethod,
                ]);

                $this->createOrderItems($order, $checkoutData['items']);

                $user->baskets()->delete();

                return $order;
            });
        } catch (\Exception $e) {
            Log::error("Ошибка создания заказа для юзера {$user->id}: ".$e->getMessage());
            throw $e;
        }
    }

    protected function createOrderItems(Order $order, Collection $items): void
    {
        foreach ($items as $item) {
            $product = $item['product'];
            $quantity = (int) $item['quantity'];

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                // Цена за единицу: товар + услуги (как в корзине)
                'price' => round($item['item_total'] / $quantity, 2),
                'quantity' => $quantity,
                'services' => $this->snapshotServices($item['selected_services']),
            ]);
        }
    }

    /**
     * @param  Collection<int, Service>  $services
     * @return list<array{id: int, name: string, price: float}>
     */
    private function snapshotServices(Collection $services): array
    {
        return $services->map(fn (Service $service) => [
            'id' => $service->id,
            'name' => $service->name,
            'price' => (float) $service->pivot->price,
        ])->values()->all();
    }
}
