<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var Product $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'slug' => $this->slug,
            'description' => $this->description,

            'price' => $this->price,

            'formatted_price' => app(CurrencyService::class)->convert($this->price),

            'image_url' => $this->image_url,

            'release_date' => $this->release_date?->format('d.m.Y'),

            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                ]);
            }),

            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(fn ($service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'pivot' => [
                        'price' => (float) $service->pivot->price,
                        'term' => $service->pivot->term,
                    ],
                ]);
            }),
        ];
    }
}
