<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\StatsService;

class SidebarStatsCacheObserver
{
    public function __construct(
        protected StatsService $statsService,
        protected CategoryService $categoryService,
    ) {}

    public function created(object $model): void
    {
        $this->forgetCaches($model);
    }

    public function deleted(object $model): void
    {
        $this->forgetCaches($model);
    }

    public function restored(object $model): void
    {
        $this->forgetCaches($model);
    }

    private function forgetCaches(object $model): void
    {
        $this->statsService->forgetSidebarStats();

        if ($model instanceof Category || $model instanceof Product) {
            $this->categoryService->forgetNavigationCache();
        }
    }
}
