<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $scale
 * @property numeric $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereScale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
