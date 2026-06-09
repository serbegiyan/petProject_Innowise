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
        $imagePath = $this->imageService->handle($image);

        if ($image && $imagePath) {
            DB::afterRollback(fn () => $this->imageService->deleteIfExists($imagePath));
        }

        return DB::transaction(function () use ($data, $imagePath, $relationData) {
            $data['image'] = $imagePath;

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
        $oldImage = $product->image;
        $imagePath = $this->imageService->handle($image, $product);

        if ($image && $imagePath) {
            DB::afterRollback(fn () => $this->imageService->deleteIfExists($imagePath));
        }

        $product = DB::transaction(function () use ($product, $data, $image, $imagePath, $relationData) {
            if ($image) {
                $data['image'] = $imagePath;
            }

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

            return $product->fresh();
        });

        if ($image && $imagePath && $oldImage && $oldImage !== $imagePath) {
            $this->imageService->deleteIfExists($oldImage);
        }

        return $product;
    }
}
