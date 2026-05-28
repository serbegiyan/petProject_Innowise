<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Export;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StatsService
{
    private const SIDEBAR_STATS_TTL = 600;

    private const SIDEBAR_STATS_DEFAULT = [
        'products_count' => 0,
        'categories_count' => 0,
        'services_count' => 0,
        'users_count' => 0,
        'orders_count' => 0,
        'exports_count' => 0,
    ];

    public function __construct(protected CacheRepository $cache) {}

    public function getSidebarStats(): array
    {
        return $this->cache->remember('sidebar_stats', self::SIDEBAR_STATS_TTL, function () {
            try {
                return [
                    'products_count' => Product::count(),
                    'categories_count' => Category::count(),
                    'services_count' => Service::count(),
                    'users_count' => User::count(),
                    'orders_count' => Order::count(),
                    'exports_count' => Export::count(),
                ];
            } catch (\Exception $e) {
                Log::warning('Sidebar stats unavailable: '.$e->getMessage());

                return self::SIDEBAR_STATS_DEFAULT;
            }
        });
    }

    public function forgetSidebarStats(): void
    {
        $this->cache->forget('sidebar_stats');
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
        $this->forgetSidebarStats();
        $this->cache->forget('nav_categories');
        $this->cache->forget('exchange_rates');
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
