<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CurrencyService
{
    private const string CACHE_KEY = 'exchange_rates';

    private readonly ?ExchangeRate $current;

    private readonly ?ExchangeRate $byn;

    public function __construct(protected CacheRepository $cache)
    {
        $this->current = $this->resolveCurrentFromSession();
        $this->byn = $this->resolveByn();
    }

    public function all(): Collection
    {
        return $this->getCachedRates();
    }

    public function getCachedRates(): Collection
    {
        if (! Schema::hasTable('exchange_rates')) {
            return collect();
        }

        return $this->cache->rememberForever(self::CACHE_KEY, function () {
            try {
                return ExchangeRate::all();
            } catch (\Exception) {
                return collect();
            }
        });
    }

    public function forgetCache(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    public function convertAmount(float|string $amountByn): float
    {
        $amount = (float) $amountByn;
        $target = $this->current ?? $this->byn;
        $base = $this->byn;

        if (! $target instanceof ExchangeRate || ! $base instanceof ExchangeRate) {
            return round($amount, 2);
        }

        return $base->convertAmountTo($target->name, $amount);
    }

    public function format(float|string $amountByn): string
    {
        $target = $this->current ?? $this->byn;
        $converted = $this->convertAmount($amountByn);

        $currencyName = $target instanceof ExchangeRate ? $target->name : 'BYN';

        return number_format($converted, 2, '.', ' ').' '.$currencyName;
    }

    public function convert(float|string $amountByn): string
    {
        return $this->format($amountByn);
    }

    public function getCurrentCurrency(): ?ExchangeRate
    {
        return $this->current;
    }

    private function resolveCurrentFromSession(): ?ExchangeRate
    {
        if (! Schema::hasTable('exchange_rates')) {
            return null;
        }

        return ExchangeRate::find(session('currency_id', 1));
    }

    private function resolveByn(): ?ExchangeRate
    {
        if (! Schema::hasTable('exchange_rates')) {
            return null;
        }

        return ExchangeRate::query()
            ->where('name', 'BYN')
            ->first()
            ?? ExchangeRate::query()->find(1);
    }
}
