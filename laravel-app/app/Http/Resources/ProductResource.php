<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
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

            'image_url' => $this->image
                ? Storage::url($this->image)
                : asset('images/product-image.png'),

            'release_date' => $this->release_date?->format('d.m.Y'),

            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                ]);
            }),
        ];
    }
}
