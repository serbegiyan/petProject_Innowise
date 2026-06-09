<?php

namespace App\Services;

use App\Models\ExchangeRate;

class CurrencyService
{
    private ?ExchangeRate $current;

    private ?ExchangeRate $byn;

    public function __construct(StatsService $stats)
    {
        $this->current = $stats->getCurrentCurrency();
        $this->byn = $this->resolveByn();
    }

    public function convertAmount(float|string $amountByn): float
    {
        $amount = (float) $amountByn;
        $target = $this->current ?? $this->byn;
        $base = $this->byn;

        if ($target === null || $base === null) {
            return round($amount, 2);
        }

        return $base->convertAmountTo($target->name, $amount);
    }

    public function format(float|string $amountByn): string
    {
        $target = $this->current ?? $this->byn;
        $converted = $this->convertAmount($amountByn);

        $currencyName = $target !== null ? $target->name : 'BYN';

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

    private function resolveByn(): ?ExchangeRate
    {
        return ExchangeRate::query()
            ->where('name', 'BYN')
            ->first()
            ?? ExchangeRate::query()->find(1);
    }
}
