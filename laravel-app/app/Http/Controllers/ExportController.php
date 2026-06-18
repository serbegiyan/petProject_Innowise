<?php

namespace App\Http\Controllers;

use App\Enums\ExportStatus;
use App\Jobs\ExportCatalogJob;
use App\Models\Export;
use App\Services\ExportService;

class ExportController extends Controller
{
    public function index(ExportService $exportService)
    {
        $exports = Export::latest()->paginate(10);

        $exports = $exportService->transformForIndex($exports);

        return view('pages.export.index', ['exports' => $exports]);
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

    public function destroy(ExportService $exportService, Export $export)
    {
        if (! $exportService->delete($export)) {
            return back()->with(
                'error',
                'Не удалось удалить экспорт. Пожалуйста, попробуйте позже или обратитесь в поддержку.',
            );
        }

        return back()->with('success', 'Экспорт успешно удален.');
    }
}
