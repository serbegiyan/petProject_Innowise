<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand,
            'slug' => $product->slug,
            'description' => $product->description,

            'price' => $product->price,

            'formatted_price' => app(CurrencyService::class)->convert($product->price),

            'image_url' => $product->image_url,

            'release_date' => $product->release_date?->format('d.m.Y'),

            'categories' => $this->whenLoaded('categories', function () use ($product) {
                return $product->categories->map(fn (Category $cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                ]);
            }),

            'services' => $this->whenLoaded('services', function () use ($product): array {
                return $product->services
                    ->map(fn (Service $service) => [
                        'id' => $service->id,
                        'name' => $service->name,
                        'description' => $service->description,
                        'pivot' => [
                            'price' => $service->pivot->price,
                            'term' => $service->pivot->term,
                        ],
                    ])
                    ->values()
                    ->all();
            }),
        ];
    }
}
