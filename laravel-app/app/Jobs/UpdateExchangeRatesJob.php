<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $url = config('services.bank.url');

        if (! $url) {
            Log::error('URL банка не настроен в конфигурации.');

            return;
        }

        Log::info('Начинаю обновление курсов...');

        ExchangeRate::updateOrCreate(
            ['id' => 1],
            ['name' => 'BYN', 'rate' => 1.0, 'scale' => 1]
        );

        try {
            $response = Http::timeout(10)
                ->retry(3, 500)
                ->get($url);

            if (! $response->successful()) {
                throw new \Exception('Ошибка HTTP: '.$response->status());
            }

            $xml = simplexml_load_string($response->body());
            if (! $xml) {
                throw new \Exception('Не удалось прочитать XML банка');
            }

            $this->parseAndSaveRates($xml);

        } catch (\Exception $e) {
            Log::warning("Не удалось обновить курсы из банка: {$e->getMessage()}. Использую дефолтные значения.");

            $this->setFallbackRates();
        }

        Cache::forget('exchange_rates');
        Log::info('Курсы обновлены и кэш очищен.');
    }

    protected function parseAndSaveRates($xml): void
    {
        $targetIso = ['USD', 'EUR', 'RUB'];

        foreach ($xml->xpath('//value') as $item) {
            $attributes = $item->attributes();
            $iso = (string) $attributes['iso'];

            if (in_array($iso, $targetIso)) {
                ExchangeRate::updateOrCreate(
                    ['name' => $iso],
                    [
                        'scale' => 1,
                        'rate' => (float) $attributes['buy'],
                    ]
                );
            }
        }
    }

    protected function setFallbackRates(): void
    {
        $defaults = [
            ['name' => 'USD', 'rate' => 2.8267, 'scale' => 1],
            ['name' => 'EUR', 'rate' => 3.3061, 'scale' => 1],
            ['name' => 'RUB', 'rate' => 0.0377, 'scale' => 1],
        ];

        foreach ($defaults as $rateData) {
            ExchangeRate::updateOrCreate(['name' => $rateData['name']], $rateData);
        }
    }
}
