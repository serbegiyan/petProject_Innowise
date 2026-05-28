<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    /**
     * Handle the Product "deleted" event.
     */
    public function deleting(Product $product)
    {
        // Если это обычное мягкое удаление — ничего не очищаем
        if (! $product->isForceDeleting()) {
            return;
        }

        // Очищаем связи в пивот-таблицах
        $product->categories()->detach();
        $product->services()->detach();

        // Удаляем файл только если путь заполнен и файл реально существует на диске
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
    }
}
