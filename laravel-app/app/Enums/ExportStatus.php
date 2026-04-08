<?php

namespace App\Enums;

enum ExportStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'В очереди',
            self::PROCESSING => 'Генерируется',
            self::COMPLETED => 'Готово',
            self::FAILED => 'Ошибка',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::PROCESSING => 'bg-blue-100 text-blue-800 border-blue-200',
            self::COMPLETED => 'bg-green-100 text-green-800 border-green-200',
            self::FAILED => 'bg-red-100 text-red-800 border-red-200',
        };
    }
}
