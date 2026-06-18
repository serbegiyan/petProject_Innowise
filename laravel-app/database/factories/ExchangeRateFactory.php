<?php

namespace Database\Factories;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExchangeRate> */
class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->currencyCode(),
            'rate' => $this->faker->randomFloat(4, 0.5, 5.0),
            'scale' => 1,
        ];
    }

    public function byn(): static
    {
        return $this->state(fn () => [
            'name' => 'BYN',
            'rate' => 1.0,
            'scale' => 1,
        ]);
    }
}
