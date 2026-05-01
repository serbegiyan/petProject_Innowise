<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'exchange_rates';

    protected $fillable = ['name', 'scale', 'rate'];

    public float $unit_rate {
        get => $this->rate / $this->scale;
    }

    public function getPriceIn(string $currency): float
    {
        $rate = ExchangeRate::where('char_code', $currency)->first();

        if (! $rate) {
            return 0.0;
        }

        return round($this->price / $rate->unit_rate, 2);
    }
}
