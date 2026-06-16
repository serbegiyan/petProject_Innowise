<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductFilterService
{
    private const CATALOG_PER_PAGE = 12;

    private const ADMIN_PER_PAGE = 10;

    /**
     * @param  array{search?: string|null, category?: int|null, category_id?: int|null, sort?: string|null}  $params
     * @param  array{per_page?: int, path?: string, with?: array<int, string>}  $options
     */
    public function filter(array $params, array $options = []): LengthAwarePaginator
    {
        $perPage = $options['per_page'] ?? self::CATALOG_PER_PAGE;
        $path = $options['path'] ?? route('catalog.index', [], false);
        $relations = $options['with'] ?? ['categories'];
        $categoryId = $params['category'] ?? $params['category_id'] ?? null;

        $query = Product::query();

        if ($relations !== []) {
            $query->with($relations);
        }

        return $query
            ->search($params['search'] ?? null)
            ->byCategory($categoryId)
            ->applySort($params['sort'] ?? null)
            ->paginate($perPage)
            ->withPath($path)
            ->withQueryString();
    }

    /**
     * @param  array{search?: string|null, category_id?: int|null}  $params
     */
    public function filterForAdmin(array $params): LengthAwarePaginator
    {
        return $this->filter($params, [
            'per_page' => self::ADMIN_PER_PAGE,
            'path' => route('product.index', [], false),
            'with' => [],
        ]);
    }
}
