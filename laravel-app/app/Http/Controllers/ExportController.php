<?php

namespace App\Http\Controllers;

use App\Enums\ExportStatus;
use App\Jobs\ExportCatalogJob;
use App\Models\Export;
use App\Models\Product;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
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
                // Размер файла в БД берется из S3 только для готовых
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
        $fileName = 'catalog_export_'.now()->format('Ymd_His').'.csv';
        $path = 'exports/'.$fileName;

        $exportRecord = Export::create([
            'status' => ExportStatus::PENDING,
            'file_name' => $fileName,
            'file_path' => $path,
        ]);

        $data = Product::all()->toArray();

        // Передаем ID воркеру
        
        ExportCatalogJob::dispatch($exportRecord->id, $data);

        return back()->with('success', 'Экспорт поставлен в очередь. Вы получите уведомление на почту.');
    }

    public function destroy($id)
    {
        $export = Export::findOrFail($id);

        try {
            if (Storage::disk('s3')->exists($export->file_path)) {
                Storage::disk('s3')->delete($export->file_path);
            }

            $export->delete();

            return back()->with('success', 'Экспорт успешно удален.');

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении: '.$e->getMessage());
        }
    }
}
