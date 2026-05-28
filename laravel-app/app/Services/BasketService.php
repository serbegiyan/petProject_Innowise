<?php

namespace App\Services;

use App\Models\Basket;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BasketService
{
    private int $userId {
        get {
            return Auth::id() ?? throw new \Exception('User not authenticated');
        }
    }

    /**
     * @return Collection<int, array{
     *     cart_id: int,
     *     product: Product,
     *     quantity: int,
     *     selected_services: \Illuminate\Database\Eloquent\Collection<int, Service>
     * }>
     */
    public function getUserBasketItems(): Collection
    {
        return Basket::where('user_id', $this->userId)
            ->with('product.services')
            ->get()
            ->map(function (Basket $item) {
                $product = $item->product;
                if (! $product instanceof Product) {
                    throw new \RuntimeException("Basket item {$item->id} has no product.");
                }

                return [
                    'cart_id' => $item->id,
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'selected_services' => $product->services->whereIn(
                        'id',
                        collect($item->services)->pluck('id')->toArray()
                    )->values(),
                ];
            });
    }

    public function getCheckoutDetails(): array
    {
        $items = $this->getUserBasketItems()->map(function ($item) {
            $servicesPrice = $item['selected_services']->sum(
                fn (Service $service) => (float) $service->pivot->price
            );

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

        $basketItem = Basket::where('user_id', $this->userId)
            ->where('product_id', $data['product_id'])
            ->get()
            ->first(fn (Basket $item) => ($item->services ?? []) === $services);

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
