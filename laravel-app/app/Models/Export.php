<?php

namespace App\Models;

use App\Enums\ExportStatus;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = ['file_name', 'file_path', 'status', 'error_message', 'size'];

    protected $table = 'exports';

    protected function casts(): array
    {
        return [
            'status' => ExportStatus::class,
        ];
    }
}
