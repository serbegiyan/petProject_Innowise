<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductFilterService
{
    private const PER_PAGE = 12;

    /**
     * Универсальный метод фильтрации
     */
    public function filter(array $params): LengthAwarePaginator
    {
        return Product::query()
            ->with('categories')
            ->search($params['search'] ?? null)
            ->byCategory($params['category'] ?? null)
            ->applySort($params['sort'] ?? null)
            ->paginate(self::PER_PAGE)
            ->withQueryString(); // Сохраняет параметры фильтра в ссылках пагинации
    }
}
