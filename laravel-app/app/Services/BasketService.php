<?php

namespace App\Services;

use App\Models\Basket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BasketService
{
    /**
     * @return \Illuminate\Support\Collection<int, array{
     *     cart_id: int,
     *     product: \App\Models\Product,
     *     quantity: int,
     *     selected_services: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service>
     * }>
     */
    public function getUserBasketItems(): Collection
    {
        return Basket::where('user_id', Auth::id())
            ->with(['product.services'])
            ->get()
            ->map(function (Basket $item) {
                // Извлекаем ID выбранных сервисов из JSON/Array колонки корзины
                $selectedIds = collect($item->services)->pluck('id')->toArray();

                // Фильтруем сервисы продукта, которые были выбраны в этой записи корзины
                $selectedServices = $item->product->services->whereIn('id', $selectedIds);

                return [
                    'cart_id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'selected_services' => $selectedServices->values(),
                ];
            });
    }

    /**
     * Подготовка данных корзины для оформления заказа (Checkout).
     *
     * @return array{items: Collection, totalAmount: float}
     */
    public function getCheckoutDetails(): array
    {
        $basketItems = Basket::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with(['product.services']) // подгружаем услуги продукта
            ->get();

        $items = $basketItems->map(function (Basket $item) {
            // 1. Получаем ID услуг, которые выбрал пользователь в корзине
            $selectedIds = collect($item->services)->pluck('id')->toArray();

            // 2. Находим полные объекты этих услуг среди всех услуг продукта
            $fullServices = $item->product->services->whereIn('id', $selectedIds)->values(); // Сбрасываем ключи для чистого JSON

            // 3. Считаем сумму этих услуг
            $servicesPrice = $fullServices->sum('price');

            return [
                'id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                // Теперь здесь не просто ID, а массив объектов {id, name, price}
                'services' => $fullServices->toArray(),
                'item_total' => ($item->product->price + $servicesPrice) * $item->quantity,
            ];
        });

        return [
            'items' => $items,
            'totalAmount' => round($items->sum('item_total'), 2),
        ];
    }

    public function addToBasket(array $data, ?int $editCartId = null): void
    {
        $userId = Auth::id();

        // 1. Если это редактирование (замена), удаляем старую запись
        if ($editCartId) {
            Basket::where('id', $editCartId)->where('user_id', $userId)->delete();
        }

        // 2. Нормализуем сервисы для корректного сравнения JSON
        $services = collect($data['services'] ?? [])
            ->sortBy('id')
            ->values()
            ->toArray();

        // 3. Ищем существующий такой же товар с такими же сервисами
        $basketItem = Basket::where('user_id', $userId)->where('product_id', $data['product_id'])->get()->filter(fn ($item) => $item->services == $services)->first();

        // 4. Логика: инкремент или создание
        if ($basketItem) {
            $basketItem->increment('quantity');
        } else {
            Basket::create([
                'user_id' => $userId,
                'product_id' => $data['product_id'],
                'services' => $services,
                'quantity' => 1,
            ]);
        }
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        Basket::where('id', $itemId)
            ->where('user_id', Auth::id())
            ->firstOrFail()
            ->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): void
    {
        Basket::where('id', $itemId)->where('user_id', Auth::id())->firstOrFail()->delete();
    }
}
