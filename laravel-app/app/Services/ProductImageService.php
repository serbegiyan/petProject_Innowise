<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function handle(Request $request, ?Product $product = null): ?string
    {
        if (!$request->hasFile('image')) {
            return $product?->image;
        }

        if ($product && $product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $request->file('image')->store('products', 'public');
    }
}
