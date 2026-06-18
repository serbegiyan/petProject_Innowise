<?php

namespace App\Services;

use App\Models\Export;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function transformForIndex(LengthAwarePaginator $exports): LengthAwarePaginator
    {
        $exports->through(function ($export) {
            /** @var Filesystem|FilesystemAdapter $disk */
            $disk = Storage::disk('s3');
            $fileExists = $export->file_path && $disk->exists($export->file_path);

            return [
                'id' => $export->id,
                'name' => $export->file_name ?? 'Генерируется...',
                'status' => $export->status,
                'url' => $fileExists ? $disk->url($export->file_path) : null,
                'date' => $export->created_at->format('d.m.Y H:i'),
                'size' => $fileExists
                    ? round($disk->size($export->file_path) / 1024, 2).' KB'
                    : '—',
            ];
        });

        return $exports;
    }

    public function delete(Export $export): bool
    {
        $filePath = $export->file_path;

        try {
            DB::transaction(function () use ($export): void {
                $export->delete();
            });
        } catch (Exception $e) {
            Log::error('Ошибка при удалении записи экспорта', [
                'export_id' => $export->id,
                'file_path' => $filePath,
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }

        try {
            if ($filePath && Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
            }
        } catch (Exception $e) {
            Log::warning('Запись экспорта удалена, но файл не удалён из S3', [
                'export_id' => $export->id,
                'file_path' => $filePath,
                'error_message' => $e->getMessage(),
            ]);
        }

        return true;
    }
}
