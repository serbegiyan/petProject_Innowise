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
    /**
     * Получить статистику для боковой панели (с кэшированием)
     */
    public function getSidebarStats(): array
    {
        // Используем Redis (через драйвер cache) для хранения тяжелых расчетов
        return Cache::remember('sidebar_stats', 600, function () {
            return [
                'products_count' => Product::count(),
                'categories_count' => Category::count(),
                'services_count' => Service::count(),
                'users_count' => User::count(),
                'orders_count' => Order::count(),
                // Запрос к Localstack S3 — теперь не тормозит рендер страницы
                'exports_count' => $this->getS3ExportsCount(),
            ];
        });
    }

    /**
     * Получить категории для навигации с количеством товаров
     */
    public function getNavCategories()
    {
        return Cache::remember('nav_categories', 600, function () {
            return Category::withCount('products')->get();
        });
    }

    /**
     * Получить курсы валют.
     * Твой Job по обновлению курсов будет сбрасывать этот кэш.
     */
    public function getExchangeRates()
    {
        return Cache::rememberForever('exchange_rates', function () {
            return ExchangeRate::all();
        });
    }

    /**
     * Принудительная очистка кэша статистики.
     * Вызывай это в конце UpdateExchangeRatesJob или после завершения экспорта в RabbitMQ.
     */
    public function clearCache(): void
    {
        Cache::forget('sidebar_stats');
        Cache::forget('nav_categories');
        Cache::forget('exchange_rates');
    }

    /**
     * Вспомогательный метод для работы с S3
     */
    private function getS3ExportsCount(): int
    {
        try {
            return count(Storage::disk('s3')->files('exports'));
        } catch (\Exception $e) {
            // Если Localstack недоступен, возвращаем 0, чтобы сайт не упал
            Log::warning('S3 Storage unavailable: '.$e->getMessage());

            return 0;
        }
    }
}
