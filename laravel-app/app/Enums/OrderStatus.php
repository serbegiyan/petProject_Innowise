<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case DONE = 'done';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидает',
            self::PROCESSING => 'В обработке',
            self::DONE => 'Выполнен',
            self::CANCELED => 'Отменен',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::PROCESSING => 'bg-blue-100 text-blue-800 border-blue-200',
            self::DONE => 'bg-green-100 text-green-800 border-green-200',
            self::CANCELED => 'bg-red-100 text-red-800 border-red-200',
        };
    }
}
