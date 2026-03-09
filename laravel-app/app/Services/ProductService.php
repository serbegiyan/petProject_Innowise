<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function create(Request $request): Product
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            $data['image'] = app(ProductImageService::class)->handle($request);

            $product = Product::create($data);

            app(ProductRelationsService::class)->syncCategories($product, $request);

            app(ProductRelationsService::class)->syncServices($product, $request);

            return $product;
        });
    }

    public function update(Product $product, Request $request): Product
    {
        return DB::transaction(function () use ($product, $request) {
            $data = $request->validated();

            $data['image'] = app(ProductImageService::class)->handle($request, $product);

            $product->update($data);

            app(ProductRelationsService::class)->syncCategories($product, $request);

            app(ProductRelationsService::class)->syncServices($product, $request);

            return $product;
        });
    }
}
