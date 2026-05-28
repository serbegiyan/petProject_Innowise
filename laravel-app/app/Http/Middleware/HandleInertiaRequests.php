<?php

namespace App\Http\Middleware;

use App\Services\StatsService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $statsService = app(StatsService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $this->getSharedUser($request),
                'currency' => fn () => $statsService->getCurrentCurrency(),
            ],
            'currencies' => fn () => $statsService->getAllCurrencies(),

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    private function getSharedUser(Request $request): ?array
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $user->loadCount('baskets');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'basket_count' => (int) $user->baskets_count,
        ];
    }
}
