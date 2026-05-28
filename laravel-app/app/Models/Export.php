<?php

namespace App\Models;

use App\Enums\ExportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'file_path', 'status', 'error_message', 'size'];

    protected $table = 'exports';

    protected function casts(): array
    {
        return [
            'status' => ExportStatus::class,
        ];
    }

    /**
     * @return array{file_name: string, file_path: string}
     */
    public static function newStoragePaths(): array
    {
        $fileName = 'catalog_export_'.now()->format('Ymd_His').'.csv';

        return [
            'file_name' => $fileName,
            'file_path' => 'exports/'.$fileName,
        ];
    }
}
