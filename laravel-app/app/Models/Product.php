<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'products';

    protected $appends = ['image_url'];

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function services(): BelongsToMany
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

    public function getImageUrlAttribute(): string
    {
        return $this->image ? Storage::url($this->image) : asset('images/product-image.png');
    }
}
