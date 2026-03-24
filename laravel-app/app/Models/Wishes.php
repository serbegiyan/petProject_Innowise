<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishes whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Wishes extends Model
{
    //
}
