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

    public function __construct(protected int $exportId,
        protected array $catalogData) {}
    
    public function handle(): void
    {
        $export = Export::find($this->exportId);

        if (! $export) {
            Log::error("Export record not found: {$this->exportId}");
            return;
        }

        try {
            $export->update(['status' => ExportStatus::PROCESSING]);

            $csvHeader = ['ID', 'Name', 'Price', 'Brand'];
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $csvHeader);

            foreach ($this->catalogData as $row) {
                fputcsv($handle, [$row['id'], $row['name'], $row['price'], $row['brand']]);
            }

            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);
            
            Storage::disk('s3')->put($export->file_path, $csvContent);

            $export->update([
                'status' => ExportStatus::COMPLETED,
            ]);

            Mail::to('admin@example.com')->send(new CatalogExported($export->file_name));

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
