<?php

namespace App\Support;

final class Money
{
    private const int SCALE = 2;

    public static function add(string|float|int ...$amounts): string
    {
        $sum = '0';

        foreach ($amounts as $amount) {
            $sum = bcadd($sum, self::normalize($amount), self::SCALE);
        }

        return $sum;
    }

    public static function mul(string|float|int $amount, int|string $multiplier): string
    {
        return bcmul(self::normalize($amount), (string) $multiplier, self::SCALE);
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

    private static function normalize(string|float|int $amount): string
    {
        if (is_string($amount)) {
            return bcadd($amount, '0', self::SCALE);
        }

        return bcadd((string) $amount, '0', self::SCALE);
    }
}
