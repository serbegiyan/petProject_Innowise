<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Export;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Log;

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
}
