<?php

namespace App\Http\Resources;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItem */
class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'quantity' => (int) $this->quantity,
            'price' => $this->price,

            'services' => collect($this->services ?? [])->map(fn (array $service) => [
                'id' => $service['id'],
                'name' => $service['name'],
                'price' => $service['price'],
            ])->values()->all(),

            'product' => $this->whenLoaded('product', function () {
                /** @var Product $product */
                $product = $this->product;

                return [
                    'id' => $product->id,
                    'image_url' => $product->image_url,
                ];
            }),
        ];
    }
}
