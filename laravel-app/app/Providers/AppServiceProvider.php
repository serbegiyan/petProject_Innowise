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
use App\Services\CategoryService;
use App\Services\CurrencyService;
use App\Services\StatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(StatsService::class);
        $this->app->singleton(CurrencyService::class);
        $this->app->singleton(CategoryService::class);
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
            $currencyService = $this->app->make(CurrencyService::class);
            $categoryService = $this->app->make(CategoryService::class);

            $view->with([
                'rates' => $currencyService->getCachedRates(),
                'sidebar_stats' => $statsService->getSidebarStats(),
                'navCategories' => $categoryService->getForNavigation(),
            ]);
        });

        View::composer('pages.home', function ($view) {
            $view->with(
                'sidebar_stats',
                $this->app->make(StatsService::class)->getSidebarStats(),
            );
        });
    }
}
