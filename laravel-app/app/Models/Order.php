<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $user_id
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $comment
 * @property numeric $total_price
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_email
 * @property string|null $customer_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_class
 * @property-read mixed $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = ['user_id', 'status', 'payment_method', 'comment', 'total_price', 'customer_name', 'customer_phone', 'customer_email', 'customer_address'];

    protected $appends = ['status_label', 'status_class'];

    public const STATUSES = [
        'pending' => 'Ожидает',
        'processing' => 'В обработке',
        'done' => 'Выполнен',
        'canceled' => 'Отменен',
    ];

    public static function getStatusOptions()
    {
        return collect(self::STATUSES)
            ->map(function ($name, $id) {
                return (object) ['id' => $id, 'name' => $name];
            })
            ->values();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status];
    }

    public function getStatusClassAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
            'done' => 'bg-green-100 text-green-800 border-green-200',
            'canceled' => 'bg-red-100 text-red-800 border-red-200',
        ][$this->status];
    }
}
