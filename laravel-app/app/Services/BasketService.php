<?php

namespace App\Services;

use App\Models\Basket;
use App\Models\Product;
use App\Models\Service;
use App\Support\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function getUserBasketItems(bool $lock = false): Collection
    {
        $query = Basket::where('user_id', $this->userId)
            ->with('product.services');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()->map(function (Basket $item) {
            $product = $item->product;
            if (! $product instanceof Product) {
                throw new \RuntimeException("Basket item {$item->id} has no product.");
            }

            $selectedIds = Basket::normalizeServiceIds($item->services);

            return [
                'cart_id' => $item->id,
                'product' => $product,
                'quantity' => $item->quantity,
                'selected_services' => $product->services->whereIn('id', $selectedIds)->values(),
            ];
        });
    }

    public function getCheckoutDetails(bool $lock = false): array
    {
        $items = $this->getUserBasketItems(lock: $lock)->map(function ($item) {
            $servicesPrice = Money::sum(
                $item['selected_services']->map(fn (Service $service) => $service->pivot->price)
            );

            $singlePrice = Money::add($item['product']->price, $servicesPrice);

            return [
                ...$item,
                'single_price' => $singlePrice,
                'item_total' => Money::mul($singlePrice, $item['quantity']),
            ];
        });

        return [
            'items' => $items,
            'totalAmount' => Money::sum($items->pluck('item_total')),
        ];
    }

    public function addToBasket(array $data, ?int $editCartId = null): void
    {
        $serviceIds = Basket::normalizeServiceIds($data['services'] ?? []);
        $servicesKey = Basket::servicesKey($serviceIds);

        DB::transaction(function () use ($data, $serviceIds, $servicesKey, $editCartId): void {
            Basket::where('user_id', $this->userId)->lockForUpdate()->get();

            if ($editCartId) {
                $this->removeItem($editCartId);
            }

            $basketItem = Basket::where('user_id', $this->userId)
                ->where('product_id', $data['product_id'])
                ->where('services_key', $servicesKey)
                ->first();

            if ($basketItem) {
                $basketItem->increment('quantity');

                return;
            }

            Basket::create([
                'user_id' => $this->userId,
                'product_id' => $data['product_id'],
                'services' => $serviceIds,
                'services_key' => $servicesKey,
                'quantity' => 1,
            ]);
        });
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

    public function clear(): void
    {
        Basket::where('user_id', $this->userId)->delete();
    }
}
