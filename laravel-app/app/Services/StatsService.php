<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StatsService
{
    public function __construct(
        protected CacheRepository $cache,
        protected StorageFactory $storage
    ) {}

    public function getSidebarStats(): array
    {
        return $this->cache->remember('sidebar_stats', 600, function () {
            try {
                return [
                    'products_count' => Product::count(),
                    'categories_count' => Category::count(),
                    'services_count' => Service::count(),
                    'users_count' => User::count(),
                    'orders_count' => Order::count(),
                    'exports_count' => $this->getS3ExportsCount(),
                ];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public function getNavCategories()
    {
        return $this->cache->remember('nav_categories', 600, function () {
            try {
                return Category::withCount('products')->get();
            } catch (\Exception $e) {
                return collect();
            }
        });
    }

    public function getExchangeRates()
    {
        if (! Schema::hasTable('exchange_rates')) {
            return collect();
        }

        return $this->cache->rememberForever('exchange_rates', function () {
            try {
                return ExchangeRate::all();
            } catch (\Exception $e) {
                return collect();
            }
        });
    }

    public function clearCache(): void
    {
        $this->cache->forget('sidebar_stats');
        $this->cache->forget('nav_categories');
        $this->cache->forget('exchange_rates');
    }

    private function getS3ExportsCount(): int
    {
        try {
            return count($this->storage->disk('s3')->files('exports'));
        } catch (\Exception $e) {
            Log::warning('S3 Storage unavailable: '.$e->getMessage());

            return 0;
        }
    }

    public function getAllCurrencies()
    {
        if (! Schema::hasTable('exchange_rates')) {
            return collect();
        }

        return ExchangeRate::all();
    }

    public function getAllCategories()
    {
        if (! Schema::hasTable('categories')) {
            return collect();
        }

        return Category::all();
    }

    public function getCurrentCurrency(): ?ExchangeRate
    {
        if (! Schema::hasTable('exchange_rates')) {
            return null;
        }

        $currencyId = session('currency_id', 1);

        // Можно добавить кеширование, если валют много
        return ExchangeRate::find($currencyId);
    }
}
