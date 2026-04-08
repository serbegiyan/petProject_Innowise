<?php

namespace App\Providers;

use App\Services\StatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StatsService::class);
    }

    public function boot(): void
    {
        // Оптимизация загрузки ресурсов Vite
        Vite::prefetch(concurrency: 3);

        /** @var StatsService $statsService */
        $statsService = $this->app->make(StatsService::class);

        // 1. Делимся курсами валют со всеми вьюхами (через кэш внутри сервиса)
        View::share('rates', $statsService->getExchangeRates());

        // 2. Настраиваем композер для главного лейаута админки
        View::composer('layouts/main', function ($view) use ($statsService) {
            $view->with([
                'sidebar_stats' => $statsService->getSidebarStats(),
                'navCategories' => $statsService->getNavCategories(),
            ]);
        });
    }
}
