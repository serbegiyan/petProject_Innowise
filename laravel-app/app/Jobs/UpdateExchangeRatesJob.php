<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        \Log::info('Начинаю загрузку XML...');

        $response = Http::withoutVerifying()->get('https://bankdabrabyt.by/export_courses.php');

        \Log::info('Ответ получен. Статус: '.$response->status());

        $xml = simplexml_load_string($response->body());

        if (! $xml) {
            \Log::error('Ошибка парсинга XML');

            return;
        }

        $xml = simplexml_load_string($response->body());

        foreach ($xml->xpath('//value') as $item) {
            $attributes = $item->attributes();
            $iso = (string) $attributes['iso'];
            // 1. Удаляем все старые записи с именем BYN (если они есть под другими ID)
            DB::table('exchange_rates')->where('name', 'BYN')->delete();

            // 2. Вставляем BYN именно под ID 1
            // Если ID 1 занят чем-то другим — сначала освобождаем его (сдвигаем старую запись)
            if (DB::table('exchange_rates')->where('id', 1)->exists()) {
                $oldRecord = DB::table('exchange_rates')->where('id', 1)->first();
                if ($oldRecord->name !== 'BYN') {
                    // Сдвигаем старую запись (например, USD) на свободный ID
                    DB::table('exchange_rates')
                        ->where('id', 1)
                        ->update(['id' => DB::table('exchange_rates')->max('id') + 1]);
                }
            }

            // 3. Теперь ID 1 либо пуст, либо там уже BYN
            DB::table('exchange_rates')->updateOrInsert(['id' => 1], ['name' => 'BYN', 'rate' => 1.0, 'scale' => 1, 'updated_at' => now()]);

            if (in_array($iso, ['USD', 'EUR', 'RUB'])) {
                ExchangeRate::updateOrCreate(
                    ['name' => $iso],
                    [
                        'scale' => 1,
                        'rate' => (float) $attributes['buy'],
                    ],
                );
            }
        }

        Log::info('Курсы валют успешно обновлены через Job.');
    }
}
