<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Администратор',
            self::USER => 'Пользователь',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::ADMIN => 'bg-green-100 text-green-800 border-green-200',
            self::USER => 'bg-purple-200 text-gray-800',
        };
    }
}
