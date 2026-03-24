<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'exchange_rates';

    protected $fillable = ['name', 'scale', 'rate'];

    // PHP 8.5 Хук для расчета реального курса
    public float $unit_rate {
        get => $this->rate / $this->scale;
    }

    public function getPriceIn(string $currency): float
    {
        $rate = ExchangeRate::where('char_code', $currency)->first();

        // Если курса нет в базе, возвращаем 0 или кидаем ошибку
        if (! $rate) {
            return 0.0;
        }

        // Используем хук unit_rate
        return round($this->price / $rate->unit_rate, 2);
    }
}
