<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property OrderStatus $status
 * @property string $total_price
 * @property string $customer_name
 * @property Carbon $created_at
 * @property-read Collection<int, OrderItem> $items
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status', 'payment_method', 'comment',
        'total_price', 'customer_name', 'customer_phone',
        'customer_email', 'customer_address',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_price' => 'decimal:2',
        ];
    }

    public static function getStatusOptions()
    {
        return collect(OrderStatus::cases())->map(fn ($status) => (object) [
            'id' => $status->value,
            'name' => $status->label(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
