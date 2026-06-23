<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRatesJob implements ShouldQueue
{
    use Queueable;

    public function handle(CurrencyService $currencyService): void
    {
        $url = config('services.bank.url');

        if (! $url) {
            Log::error('URL банка не настроен в конфигурации.');

            return;
        }

        Log::info('Начинаю обновление курсов...');

        ExchangeRate::updateOrCreate(
            ['id' => 1],
            ['name' => 'BYN', 'rate' => '1.0000', 'scale' => 1]
        );

        try {
            $response = Http::timeout(10)
                ->retry(3, 500)
                ->get($url);

            if (! $response->successful()) {
                throw new \Exception('Ошибка HTTP: '.$response->status());
            }

            // В PHP 8.4+ внешние сущности отключены по умолчанию.
            // Флаг LIBXML_NONET блокирует любые сетевые запросы во время парсинга (Hardening).
            $xml = simplexml_load_string(
                $response->body(),
                'SimpleXMLElement',
                LIBXML_NONET
            );

            if (! $xml) {
                throw new \Exception('Не удалось прочитать XML банка или XML поврежден');
            }

            $this->parseAndSaveRates($xml);

        } catch (\Exception $e) {
            Log::warning("Не удалось обновить курсы из банка: {$e->getMessage()}. Использую дефолтные значения.");

            $this->setFallbackRates();
        }

        $currencyService->forgetCache();
        Log::info('Курсы обновлены и кэш очищен.');
    }

    protected function parseAndSaveRates($xml): void
    {
        $targetIso = config('services.target_currencies', ['USD', 'EUR', 'RUB']);

        foreach ($xml->xpath('//value') as $item) {
            $attributes = $item->attributes();
            $iso = (string) $attributes['iso'];

            if (in_array($iso, $targetIso)) {
                // Пытаемся получить реальный масштаб из XML (например, атрибут scale или quantity)
                // Если в вашем XML его нет, уточните, где банк передает кратность (например, за 100 рублей)
                $scale = isset($attributes['scale']) ? (int) $attributes['scale'] : 1;

                ExchangeRate::updateOrCreate(
                    ['name' => $iso],
                    [
                        'scale' => $scale,
                        'rate' => (string) $attributes['buy'], // Сохраняем как string для точности decimal
                    ]
                );
            }
        }
    }

    protected function setFallbackRates(): void
    {
        $fallbackMap = [
            'USD' => ['rate' => '2.8267', 'scale' => 1],
            'EUR' => ['rate' => '3.3061', 'scale' => 1],
            'RUB' => ['rate' => '0.0377', 'scale' => 1],
        ];

        $targetIso = config('services.target_currencies', ['USD', 'EUR', 'RUB']);

        foreach ($targetIso as $code) {
            $fallbackData = $fallbackMap[$code] ?? ['rate' => '1.0000', 'scale' => 1];

            ExchangeRate::updateOrCreate(
                ['name' => $code],
                [
                    'scale' => $fallbackData['scale'],
                    'rate' => $fallbackData['rate'],
                ]
            );
        }
    }
}
