<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StatsService
{
    public function getSidebarStats(): array
    {
        return Cache::remember('sidebar_stats', 600, function () {
            return [
                'products_count' => Product::count(),
                'categories_count' => Category::count(),
                'services_count' => Service::count(),
                'users_count' => User::count(),
                'orders_count' => Order::count(),
                'exports_count' => $this->getS3ExportsCount(),
            ];
        });
    }

    public function getNavCategories()
    {
        return Cache::remember('nav_categories', 600, function () {
            return Category::withCount('products')->get();
        });
    }

    public function getExchangeRates()
    {
        return Cache::rememberForever('exchange_rates', function () {
            return ExchangeRate::all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('sidebar_stats');
        Cache::forget('nav_categories');
        Cache::forget('exchange_rates');
    }

    private function getS3ExportsCount(): int
    {
        try {
            return count(Storage::disk('s3')->files('exports'));
        } catch (\Exception $e) {
            Log::warning('S3 Storage unavailable: '.$e->getMessage());

            return 0;
        }
    }
}
