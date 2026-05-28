<?php

namespace App\Observers;

use App\Services\StatsService;

class SidebarStatsCacheObserver
{
    public function __construct(protected StatsService $statsService) {}

    public function created(): void
    {
        $this->statsService->forgetSidebarStats();
    }

    public function deleted(): void
    {
        $this->statsService->forgetSidebarStats();
    }

    public function restored(): void
    {
        $this->statsService->forgetSidebarStats();
    }
}
