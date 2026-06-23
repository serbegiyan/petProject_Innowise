<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Mail\CatalogExported;
use App\Models\Export;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExportCatalogJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(protected int $exportId) {}

    public function handle(): void
    {
        $export = Export::find($this->exportId);

        if (! $export) {
            Log::error("Export record not found: {$this->exportId}");

            return;
        }

        $recipient = config('mail.catalog_export_recipient');
        if (empty($recipient) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->markAsFailed(
                $export,
                "Некорректный или пустой получатель экспорта в конфиге: '".($recipient ?? 'null')."'"
            );

            return;
        }

        if (! $export->file_path) {
            $this->markAsFailed($export, 'Не задан путь для сохранения файла.');

            return;
        }

        try {
            $export->update(['status' => ExportStatus::PROCESSING]);

            $handle = tmpfile();

            $csvHeader = ['ID', 'Name', 'Price', 'Brand'];
            fputcsv($handle, $csvHeader, escape: '\\');

            Product::query()
                ->select(['id', 'name', 'price', 'brand'])
                ->orderBy('id')
                ->chunk(500, function ($products) use ($handle): void {
                    foreach ($products as $product) {
                        fputcsv($handle, [
                            $product->id,
                            $product->name,
                            $product->price,
                            $product->brand,
                        ],
                            escape: '\\');
                    }
                });

            rewind($handle);

            Storage::disk('s3')->writeStream($export->file_path, $handle);

            $stats = fstat($handle);
            $fileSize = $stats['size'] ?? 0;

            fclose($handle);

            Mail::to($recipient)
                ->send(new CatalogExported($export->file_path));

            $export->update([
                'status' => ExportStatus::COMPLETED,
                'size' => $fileSize,
            ]);

        } catch (Throwable $e) {
            $this->markAsFailed($export, $e->getMessage());
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $export = Export::find($this->exportId);
        if ($export) {
            $this->markAsFailed($export, $exception->getMessage());
        }
    }

    protected function markAsFailed(Export $export, string $message): void
    {
        $export->update([
            'status' => ExportStatus::FAILED,
            'error_message' => mb_substr($message, 0, 255),
        ]);

        Log::error("Export ID {$export->id} failed: ".$message);
    }
}
