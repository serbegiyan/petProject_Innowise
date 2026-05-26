<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\StatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StatsService::class);
        Product::observe(ProductObserver::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        View::composer('*', function ($view) {
            $statsService = $this->app->make(StatsService::class);
            $view->with('rates', $statsService->getExchangeRates());
        });

        View::composer('layouts/main', function ($view) {
            $statsService = $this->app->make(StatsService::class);
            $view->with([
                'sidebar_stats' => $statsService->getSidebarStats(),
                'navCategories' => $statsService->getNavCategories(),
            ]);
        });
    }
}
