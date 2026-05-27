<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function handle(?UploadedFile $image, ?Product $product = null): ?string
    {
        // Если новый файл не загружен, сохраняем старую картинку
        if (! $image) {
            return $product?->image;
        }

        // Если есть старая картинка, удаляем её перед записью новой
        if ($product && $product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Сохраняем файл на диск public в папку products
        return $image->store('products', 'public');
    }
}
