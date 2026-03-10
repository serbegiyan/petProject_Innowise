<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductRelationsService
{
    public function syncCategories(Product $product, Request $request): void
    {
        $product->categories()->sync($request->category_id);
    }

    public function syncServices(Product $product, Request $request): void
    {
        $servicesData = [];

        foreach ($request->services ?? [] as $serviceId) {
            $servicesData[$serviceId] = [
                'price' => $request->service_prices[$serviceId] ?? 0,
                'term' => $request->service_terms[$serviceId] ?? 'не указан',
            ];
        }

        $product->services()->sync($servicesData);
    }
}
