<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        View::composer('layouts/main', function ($view) {
            $stats = [
                'products_count' => Product::count(),
                'categories_count' => Category::count(),
                'services_count' => Service::count(),
                'users_count' => User::count(),
            ];
            $navCategories = Category::withCount('products')->get();

            $view->with([
                'sidebar_stats' => $stats,
                'navCategories' => $navCategories,
            ]);
        });
    }
}
