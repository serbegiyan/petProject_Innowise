<?php

namespace App\Http\Middleware;

use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    #[\Override]
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    #[\Override]
    public function share(Request $request): array
    {
        $currencyService = app(CurrencyService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $this->getSharedUser($request),
                'currency' => fn () => $this->currencyDto($currencyService->getCurrentCurrency()),
            ],
            'currencies' => fn () => $currencyService->all()
                ->map(fn (ExchangeRate $rate) => $rate->toCurrencyDto())
                ->values()
                ->all(),

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
            'is_Admin' => $user->isAdmin(),
            'basket_count' => (int) $user->baskets_count,
        ];
    }

    /**
     * @return array{id: int, name: string, unit_rate: float}|null
     */
    private function currencyDto(?ExchangeRate $rate): ?array
    {
        return $rate?->toCurrencyDto();
    }
}
