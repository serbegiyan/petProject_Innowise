<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        protected ProductImageService $imageService,
        protected ProductRelationsService $relationsService
    ) {}

    public function create(array $data, ?UploadedFile $image, array $relationData): Product
    {
        return DB::transaction(function () use ($data, $image, $relationData) {
            $data['image'] = $this->imageService->handle($image);

            $product = Product::create($data);

            $this->relationsService->syncCategories($product, $relationData['category_ids'] ?? []);

            $this->relationsService->syncServices(
                $product,
                $relationData['services'] ?? [],
                $relationData['service_prices'] ?? [],
                $relationData['service_terms'] ?? []
            );

            return $product;
        });
    }

    public function update(Product $product, array $data, ?UploadedFile $image, array $relationData): Product
    {
        return DB::transaction(function () use ($product, $data, $image, $relationData) {
            $data['image'] = $this->imageService->handle($image, $product);

            $product->update($data);

            $this->relationsService->syncCategories(
                $product,
                $relationData['category_ids'] ?? []
            );

            $this->relationsService->syncServices(
                $product,
                $relationData['services'] ?? [],
                $relationData['service_prices'] ?? [],
                $relationData['service_terms'] ?? []
            );

            return $product;
        });
    }
}
