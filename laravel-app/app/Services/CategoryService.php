<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CategoryService
{
    private const NAV_CACHE_KEY = 'nav_categories';

    private const NAV_TTL = 600;

    public function __construct(protected CacheRepository $cache) {}

    public function getForNavigation(): Collection
    {
        return $this->cache->remember(self::NAV_CACHE_KEY, self::NAV_TTL, function () {
            try {
                return Category::withCount('products')->get();
            } catch (\Exception $e) {
                return collect();
            }
        });
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
}
