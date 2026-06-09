<?php

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Mail\CatalogExported;
use App\Models\Export;
use App\Models\Product;
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

    public function __construct(protected int $exportId) {}

    public function handle(): void
    {
        $export = Export::find($this->exportId);

        if (! $export) {
            Log::error("Export record not found: {$this->exportId}");

            return;
        }

        if (! $export->file_path) {
            Log::error("Export ID {$this->exportId} has no file_path");

            $export->update([
                'status' => ExportStatus::FAILED,
                'error_message' => 'Не задан путь для сохранения файла.',
            ]);

            return;
        }

        try {
            $export->update(['status' => ExportStatus::PROCESSING]);

            $csvHeader = ['ID', 'Name', 'Price', 'Brand'];
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $csvHeader);

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
                        ]);
                    }
                });

            rewind($handle);
            Storage::disk('s3')->writeStream($export->file_path, $handle);
            fclose($handle);

            $export->update([
                'status' => ExportStatus::COMPLETED,
            ]);

            Mail::to(config('mail.catalog_export_recipient'))
                ->send(new CatalogExported($export->file_path));

            Log::info("Export ID {$this->exportId} completed successfully.");

        } catch (\Exception $e) {
            $export->update([
                'status' => ExportStatus::FAILED,
                'error_message' => $e->getMessage(),
            ]);
            Log::error("Export ID {$this->exportId} failed: ".$e->getMessage());
        }
    }
}
