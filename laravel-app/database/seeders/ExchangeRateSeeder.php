<?php

namespace Database\Seeders;

use App\Jobs\UpdateExchangeRatesJob;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        ExchangeRate::factory()->byn()->create(['id' => 1]);

        $defaults = [
            ['name' => 'USD', 'rate' => 2.8267],
            ['name' => 'EUR', 'rate' => 3.3061],
            ['name' => 'RUB', 'rate' => 0.0377],
        ];

        foreach ($defaults as $rate) {
            ExchangeRate::factory()->create($rate);
        }

        try {
            UpdateExchangeRatesJob::dispatchSync();
        } catch (\Exception $e) {
            $this->command->warn('Банк недоступен, оставлены дефолтные курсы.');
        }
    }
}
