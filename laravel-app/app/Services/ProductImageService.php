<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function handle(?UploadedFile $image, ?Product $product = null): ?string
    {
        if (! $image) {
            return $product?->image;
        }

        return $this->store($image);
    }

    public function store(UploadedFile $image): string
    {
        return $image->store('products', 'public');
    }

    public function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
