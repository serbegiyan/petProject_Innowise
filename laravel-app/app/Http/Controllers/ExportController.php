<?php

namespace App\Http\Controllers;

use App\Enums\ExportStatus;
use App\Jobs\ExportCatalogJob;
use App\Models\Export;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index()
    {
        $exports = Export::latest()->paginate(10);

        $exports->through(function ($export) {
            /** @var Filesystem|FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            return [
                'id' => $export->id,
                'name' => $export->file_name ?? 'Генерируется...',
                'status' => $export->status,
                'url' => $export->file_path ? $disk->url($export->file_path) : null,
                'date' => $export->created_at->format('d.m.Y H:i'),
                'size' => ($export->file_path && $disk->exists($export->file_path))
                            ? round($disk->size($export->file_path) / 1024, 2).' KB'
                            : '—',
                'error' => $export->error_message,
            ];
        });

        return view('pages.export.index', compact('exports'));
    }

    public function export()
    {
        $exportRecord = Export::create([
            'status' => ExportStatus::PENDING,
            ...Export::newStoragePaths(),
        ]);

        ExportCatalogJob::dispatch($exportRecord->id);

        return back()->with('success', 'Экспорт поставлен в очередь. Вы получите уведомление на почту.');
    }

    public function destroy(Export $export)
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

            return redirect()
                ->back()
                ->with('error', 'Не удалось удалить экспорт. Пожалуйста, попробуйте позже или обратитесь в поддержку.');
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

        return back()->with('success', 'Экспорт успешно удален.');
    }
}
