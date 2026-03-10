<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Service;
use App\Services\ProductImageService;
use App\Services\ProductRelationsService;

class ProductService
{
    public function create(array $data, Request $request): Product
    {
        return DB::transaction(function () use ($data, $request) {
            $data['image'] = app(ProductImageService::class)->handle($request);

            $product = Product::create($data);

            app(ProductRelationsService::class)->syncCategories($product, $request);

            app(ProductRelationsService::class)->syncServices($product, $request);

            return $product;
        });
    }

    public function update(Product $product, array $data, Request $request): Product
    {
        return DB::transaction(function () use ($product, $data, $request) {
            $data['image'] = app(ProductImageService::class)->handle($request, $product);

            $product->update($data);

            app(ProductRelationsService::class)->syncCategories($product, $request);

            app(ProductRelationsService::class)->syncServices($product, $request);

            return $product;
        });
    }
}
