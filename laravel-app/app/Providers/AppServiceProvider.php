<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Export;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Observers\SidebarStatsCacheObserver;
use App\Services\CurrencyService;
use App\Services\StatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StatsService::class);
        $this->app->singleton(CurrencyService::class);
    }

    public function boot(): void
    {
        Product::observe(ProductObserver::class);

        $sidebarStatsObserver = SidebarStatsCacheObserver::class;
        Product::observe($sidebarStatsObserver);
        Category::observe($sidebarStatsObserver);
        Service::observe($sidebarStatsObserver);
        User::observe($sidebarStatsObserver);
        Order::observe($sidebarStatsObserver);
        Export::observe($sidebarStatsObserver);

        Vite::prefetch(concurrency: 3);

        View::composer('layouts/main', function ($view) {
            $statsService = $this->app->make(StatsService::class);

            $view->with([
                'rates' => $statsService->getExchangeRates(),
                'sidebar_stats' => $statsService->getSidebarStats(),
                'navCategories' => $statsService->getNavCategories(),
            ]);
        });
    }
}
