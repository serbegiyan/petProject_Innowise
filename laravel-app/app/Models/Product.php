<?php

namespace App\Models;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['image_url', 'formatted_price'];

    protected $fillable = ['name', 'price', 'brand', 'description', 'image', 'release_date', 'slug'];

    // Фильтрация

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"));
    }

    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, function ($q) use ($categoryId): void {
            $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId));
        });
    }

    public function scopeApplySort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'release_asc' => $query->orderBy('release_date', 'asc'),
            'release_desc' => $query->orderBy('release_date', 'desc'),
            default => $query->latest(),
        };
    }

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
        static::deleting(function ($product): void {
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

    // Привязка к сервису CurrencyService
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(get: fn () => app(CurrencyService::class)->convert($this->price));
    }
}
