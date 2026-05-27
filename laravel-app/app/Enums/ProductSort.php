<?php

namespace App\Enums;

enum ProductSort: string
{
    case PRICE_ASC = 'price_asc';
    case PRICE_DESC = 'price_desc';
    case RELEASE_ASC = 'release_asc';
    case RELEASE_DESC = 'release_desc';
    case DEFAULT = 'default';

    /**
     * Текстовые метки для интерфейса React.
     */
    public function label(): string
    {
        return match ($this) {
            self::PRICE_ASC => 'Сначала дешевые',
            self::PRICE_DESC => 'Сначала дорогие',
            self::RELEASE_ASC => 'Сначала старые',
            self::RELEASE_DESC => 'Сначала новинки',
            self::DEFAULT => 'По умолчанию',
        };
    }

    /**
     * Формирует массив опций для селекта в React.
     */
    public static function options(): array
    {
        return collect(self::cases())->map(fn ($sort) => [
            'value' => $sort->value,
            'label' => $sort->label(),
        ])->toArray();
    }
}
