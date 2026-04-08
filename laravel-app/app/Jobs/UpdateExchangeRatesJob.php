<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('Начинаю обновление курсов...');

        // 1. Сначала гарантируем наличие BYN под ID 1 (вне цикла!)
        // updateOrInsert сделает всё за один запрос и не будет постоянно удалять/вставлять
        DB::table('exchange_rates')->updateOrInsert(
            ['id' => 1],
            ['name' => 'BYN', 'rate' => 1.0, 'scale' => 1, 'updated_at' => now()]
        );

        $response = Http::withoutVerifying()->get('https://bankdabrabyt.by/export_courses.php');

        if (! $response->successful()) {
            Log::error('Ошибка загрузки курсов: '.$response->status());

            return;
        }

        $xml = simplexml_load_string($response->body());
        if (! $xml) {
            return;
        }

        // 2. Обрабатываем только нужные валюты
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

        // 3. СБРАСЫВАЕМ КЭШ (очень важно для AppServiceProvider)
        Cache::forget('exchange_rates');

        Log::info('Курсы обновлены и кэш очищен.');
    }
}
