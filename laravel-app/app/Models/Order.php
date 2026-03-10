<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
