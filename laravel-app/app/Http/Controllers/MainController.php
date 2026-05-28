<?php

namespace App\Http\Controllers;

use App\Services\StatsService;

class MainController extends Controller
{
    public function __construct(protected StatsService $statsService) {}

    public function main()
    {
        $stats = $this->statsService->getSidebarStats();

        return view('pages.home', [
            'products' => $stats['products_count'],
            'categories' => $stats['categories_count'],
            'services' => $stats['services_count'],
            'users' => $stats['users_count'],
            'orders' => $stats['orders_count'],
            'exports' => $stats['exports_count'],
        ]);
    }
}
