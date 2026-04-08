<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Mail\CatalogExported;
use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ExportCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Служба 1 отправляет сюда данные.
     */
    public function __construct(protected int $exportId,
        protected array $catalogData) {}

    /**
     * Служба 2 (воркер) выполняет этот метод.
     */
    public function handle(): void
    {
        // 1. Находим запись в БД по ID
        $export = Export::find($this->exportId);

        if (! $export) {
            Log::error("Export record not found: {$this->exportId}");

            return;
        }

        try {
            // 2. Меняем статус на "В процессе"
            $export->update(['status' => ExportStatus::PROCESSING]);

            // --- Логика генерации CSV ---
            $csvHeader = ['ID', 'Name', 'Price', 'Brand'];
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $csvHeader);

            foreach ($this->catalogData as $row) {
                fputcsv($handle, [$row['id'], $row['name'], $row['price'], $row['brand']]);
            }

            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            // 3. Сохраняем в S3, используя путь из базы данных ($export->file_path)
            // Контроллер уже записал туда правильный путь, например 'exports/catalog_export_123.csv'
            Storage::disk('s3')->put($export->file_path, $csvContent);

            // 4. УСПЕХ: Обновляем только статус (имя и путь уже там есть)
            $export->update([
                'status' => ExportStatus::COMPLETED,
            ]);

            // 5. Отправляем письмо
            Mail::to('admin@example.com')->send(new CatalogExported($export->file_name));

            Log::info("Export ID {$this->exportId} completed successfully.");

        } catch (\Exception $e) {
            // 6. ОШИБКА: Записываем текст ошибки
            $export->update([
                'status' => ExportStatus::FAILED,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Export ID {$this->exportId} failed: ".$e->getMessage());
        }
    }
}
