<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    protected $currentCurrency;

    public function __construct()
    {
        $currencyId = Session::get('currency_id', 1);
        $this->currentCurrency = ExchangeRate::find($currencyId);
    }

    public function convert(float $amount): string
    {
        if (! $this->currentCurrency) {
            return number_format($amount, 2).' BYN';
        }

        $converted = $amount / $this->currentCurrency->rate;

        return number_format($converted, 2).' '.$this->currentCurrency->name;
    }

    public function getCurrentCurrency()
    {
        return $this->currentCurrency;
    }
}
