<?php

namespace App\Services;

use App\Models\Product;

class ProductRelationsService
{
    public function syncCategories(Product $product, array|int $categoryIds): void
    {
        $product->categories()->sync((array) $categoryIds);
    }

    public function syncServices(Product $product, array $services, array $prices, array $terms): void
    {
        $servicesData = [];

        foreach ($services as $serviceId) {
            $servicesData[$serviceId] = [
                'price' => $prices[$serviceId] ?? 0,
                'term' => $terms[$serviceId] ?? 'не указан',
            ];
        }

        $product->services()->sync($servicesData);
    }
}
