<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
