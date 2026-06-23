<?php

namespace App\Support;

final class Money
{
    private const int SCALE = 2;

    private const int INTERNAL_SCALE = 4;

    public static function add(string|float|int ...$amounts): string
    {
        $sum = '0';

        foreach ($amounts as $amount) {
            $sum = bcadd($sum, self::normalize($amount), self::SCALE);
        }

        return $sum;
    }

    public static function mul(string|float|int $amount, int|float|string $multiplier): string
    {
        // 1. Нормализуем сумму до стандартных 2 знаков
        $normalizedAmount = self::normalize($amount);

        // 2. Множитель нормализуем с большей точностью (INTERNAL_SCALE),
        // чтобы не потерять дробную часть (например, 0.1234)
        $normalizedMultiplier = self::normalize($multiplier, self::INTERNAL_SCALE);

        // 3. Умножаем с повышенной точностью
        $result = bcmul($normalizedAmount, $normalizedMultiplier, self::INTERNAL_SCALE);

        // 4. Округляем финальный результат до целевых 2 знаков
        return bcadd($result, '0', self::SCALE);
    }

    /**
     * @param  iterable<string|float|int>  $amounts
     */
    public static function sum(iterable $amounts): string
    {
        $sum = '0';

        foreach ($amounts as $amount) {
            $sum = bcadd($sum, self::normalize($amount), self::SCALE);
        }

        return $sum;
    }

    public static function round(string|float|int $amount): string
    {
        return self::normalize($amount);
    }

    // Добавили необязательный параметр $scale, чтобы метод стал гибче
    private static function normalize(string|float|int $amount, int $scale = self::SCALE): string
    {
        // Если это float, используем sprintf, чтобы избежать поломки из-за научной нотации (например, 1.0E-5)
        if (is_float($amount)) {
            $amount = sprintf("%.{$scale}f", $amount);
        }

        return bcadd((string) $amount, '0', $scale);
    }
}
