<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property array<array-key, mixed>|null $services
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Basket whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Basket extends Model
{
    protected $table = 'baskets';

    protected $fillable = ['user_id', 'product_id', 'quantity', 'services'];

    protected $casts = [
        'services' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
