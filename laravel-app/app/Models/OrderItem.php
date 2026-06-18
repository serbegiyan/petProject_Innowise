<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property string $product_name
 * @property int $quantity
 * @property string $price
 * @property array<int, array{id: int, name: string, price: string}>|null $services
 * @property-read Product|null $product
 */
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = ['order_id', 'product_id', 'product_name', 'quantity', 'price', 'services'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'services' => 'array',
        ];
    }
}
