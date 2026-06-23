<?php

namespace App\Models;

use Database\Factories\ExchangeRateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /** @use HasFactory<ExchangeRateFactory> */
    use HasFactory;

    protected $table = 'exchange_rates';

    protected $fillable = ['name', 'scale', 'rate'];

    public float $unit_rate {
        get => $this->rate / $this->scale;
    }

    /**
     * @return array{id: int, name: string, unit_rate: float}
     */
    public function toCurrencyDto(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit_rate' => $this->unit_rate,
        ];
    }

    /**
     * Конвертирует сумму, уже выраженную в BYN, в целевую валюту.
     * Курс хранится как BYN за 1 единицу валюты (unit_rate = rate / scale).
     */
    public static function convertFromByn(float $amountByn, self $target): float
    {
        if (strcasecmp($target->name, 'BYN') === 0) {
            return round($amountByn, 2);
        }

        if ($target->unit_rate <= 0) {
            return 0.0;
        }

        return round($amountByn / $target->unit_rate, 2);
    }

    /**
     * Конвертирует сумму из валюты этой записи ($this->name) в целевую валюту.
     * Курс хранится как BYN за 1 единицу валюты
     */
    public function convertAmountTo(string $targetCurrency, float $amount): float
    {
        if (strcasecmp($this->name, $targetCurrency) === 0) {
            return round($amount, 2);
        }

        $target = static::query()->where('name', $targetCurrency)->first();

        if ($target === null || $this->unit_rate <= 0 || $target->unit_rate <= 0) {
            return 0.0;
        }

        $inByn = $amount * $this->unit_rate;

        return round($inByn / $target->unit_rate, 2);
    }
}
