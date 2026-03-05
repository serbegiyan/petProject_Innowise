<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $table = 'orders';
    protected $fillable = ['user_id', 'status', 'payment_method', 'comment', 'total_price', 'customer_name', 'customer_phone', 'customer_email', 'customer_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
