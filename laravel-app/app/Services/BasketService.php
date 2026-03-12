<?php

namespace App\Services;

use App\Models\Basket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BasketService
{
    // PHP 8.5: Свойство с хуком для быстрого доступа к ID текущего юзера
    private int $userId {
        get {
            return Auth::id() ?? throw new \Exception('User not authenticated');
        }
    }

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
            return Basket::where('user_id', $this->userId)
                ->with('product.services')
                ->get()
                ->map(fn (Basket $item) => [
                    'cart_id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    // PHP 8.4: array_find упрощает поиск внутри вложенных структур
                    'selected_services' => $item->product->services->whereIn(
                        'id',
                        collect($item->services)->pluck('id')->toArray()
                    )->values(),
                ]);
        }

    public function getCheckoutDetails(): array
    {
        $items = $this->getUserBasketItems()->map(function ($item) {
            $servicesPrice = $item['selected_services']->sum('price');

            return [
                ...$item,
                'services' => $item['selected_services']->toArray(),
                'item_total' => ($item['product']->price + $servicesPrice) * $item['quantity'],
            ];
        });

        return [
            'items' => $items,
            'totalAmount' => round($items->sum('item_total'), 2),
        ];
    }

    public function addToBasket(array $data, ?int $editCartId = null): void
    {
        if ($editCartId) {
            Basket::where('id', $editCartId)->where('user_id', $this->userId)->delete();
        }

        $services = collect($data['services'] ?? [])->sortBy('id')->values()->toArray();

        // PHP 8.5: Используем именованные аргументы и упрощенный поиск
        $basketItem = Basket::where('user_id', $this->userId)
            ->where('product_id', $data['product_id'])
            ->get()
            // Нативный поиск по условию (PHP 8.4+)
            ->first(fn ($item) => $item->services === $services);

        if ($basketItem) {
            $basketItem->increment('quantity');

            return;
        }

        Basket::create([
            'user_id' => $this->userId,
            'product_id' => $data['product_id'],
            'services' => $services,
            'quantity' => 1,
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        Basket::where('id', $itemId)
            ->where('user_id', $this->userId)
            ->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): void
    {
        Basket::where('id', $itemId)
            ->where('user_id', $this->userId)
            ->delete();
    }
}
