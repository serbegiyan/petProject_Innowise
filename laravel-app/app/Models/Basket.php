<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<mixed>|null $services
 * @property string $services_key
 */
class Basket extends Model
{
    use HasFactory;

    protected $table = 'baskets';

    protected $fillable = ['user_id', 'product_id', 'quantity', 'services', 'services_key'];

    #[\Override]
    protected static function booted(): void
    {
        static::saving(function (Basket $basket): void {
            $basket->services_key = self::servicesKey($basket->services);
        });
    }

    /**
     * @param  array<mixed>|null  $services
     * @return list<int>
     */
    public static function normalizeServiceIds(?array $services): array
    {
        if ($services === null || $services === []) {
            return [];
        }

        return collect($services)
            ->map(fn ($item) => is_array($item) ? (int) ($item['id'] ?? 0) : (int) $item)
            ->filter(fn (int $id) => $id > 0)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<mixed>|null  $services
     */
    public static function servicesKey(?array $services): string
    {
        $ids = self::normalizeServiceIds($services);

        return $ids === [] ? '[]' : implode(',', $ids);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'services' => 'array',
        ];
    }
}
