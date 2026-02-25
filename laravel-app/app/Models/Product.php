<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasSlug;
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'brand', 'description', 'image', 'release_date', 'slug'];

    /**
     * Настройки генерации слага
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Позволяет Laravel искать модель по слагу в URL автоматически
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withPivot('price', 'term');
    }
    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->categories()->detach();
            $product->services()->detach();
            // Логика удаления файла при событии удаления модели
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }
}
