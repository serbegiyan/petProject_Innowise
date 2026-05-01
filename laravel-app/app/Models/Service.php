<?php

namespace App\Models;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'services';

    protected $fillable = ['name', 'description', 'slug'];

    protected $appends = ['formatted_price'];

    /**
     * Настройки генерации слага
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    // Привязка к сервису CurrencyService
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(get: fn () => app(CurrencyService::class)->convert($this->pivot->price));
    }
}
