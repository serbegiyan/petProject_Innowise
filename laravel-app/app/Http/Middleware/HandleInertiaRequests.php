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
        // Получаем сервис один раз
        $statsService = app(StatsService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $this->getSharedUser($request),
                // ✅ Используем защищенный метод сервиса (нужно добавить его в StatsService)
                'currency' => fn () => $statsService->getCurrentCurrency(),
            ],
            // ✅ Список всех валют
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

        // Загружаем корзину один раз
        $basket = $user->baskets()->with('product')->get();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'basket' => $basket,
            // ✅ Считаем количество элементов в коллекции, а не в базе
            'basket_count' => $basket->count(),
        ];
    }
}
