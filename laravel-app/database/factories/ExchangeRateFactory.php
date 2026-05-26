<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangeRateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->currencyCode(),
            'rate' => $this->faker->randomFloat(4, 0.5, 5.0),
            'scale' => 1,
        ];
    }

    public function byn(): self
    {
        return $this->state(fn () => [
            'name' => 'BYN',
            'rate' => 1.0,
            'scale' => 1,
        ]);
    }
}
