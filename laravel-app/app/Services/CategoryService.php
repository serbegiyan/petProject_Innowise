<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CategoryService
{
    private const string NAV_CACHE_KEY = 'nav_categories';

    private const int NAV_TTL = 600;

    public function __construct(protected CacheRepository $cache) {}

    public function getForNavigation(): Collection
    {
        return $this->cache->remember(self::NAV_CACHE_KEY, self::NAV_TTL, function () {
            try {
                return Category::withCount('products')->get();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Category::latest()->paginate($perPage);
    }

    public function getAll(): Collection
    {
        if (! Schema::hasTable('categories')) {
            return collect();
        }

        return Category::all();
    }

    public function forgetNavigationCache(): void
    {
        $this->cache->forget(self::NAV_CACHE_KEY);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
