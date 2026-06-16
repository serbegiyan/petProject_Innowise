<?php

namespace App\Models;

use App\Enums\ProductSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read string $image_url
 * @property Carbon|null $release_date
 * @property-read Collection<int, Category> $categories
 * @property-read Collection<int, Service> $services
 */
class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = ['name', 'price', 'brand', 'description', 'image', 'release_date', 'slug'];

    protected $appends = ['image_url'];

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
        ];
    }

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    // Фильтрация

    public function scopeSearch(Builder $query, ?string $search = null): Builder
    {
        if (blank($search)) {
            return $query;
        }

        $formattedSearch = collect(explode(' ', trim($search)))
            ->filter()
            ->map(fn ($word) => "{$word}*")
            ->implode(' ');

        return $query->whereFullText('name', $formattedSearch, ['mode' => 'boolean']);
    }

    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, function ($q) use ($categoryId): void {
            $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId));
        });
    }

    public function scopeApplySort(Builder $query, ?string $sort): Builder
    {
        $sortEnum = ProductSort::tryFrom($sort) ?? ProductSort::DEFAULT;

        return match ($sortEnum) {
            ProductSort::PRICE_ASC => $query->orderBy('price', 'asc'),
            ProductSort::PRICE_DESC => $query->orderBy('price', 'desc'),
            ProductSort::RELEASE_ASC => $query->orderBy('release_date', 'asc'),
            ProductSort::RELEASE_DESC => $query->orderBy('release_date', 'desc'),
            ProductSort::DEFAULT => $query->latest(),
        };
    }

    // Настройки генерации слага

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->image) {
                    return asset('images/product-image.png');
                }

                // Если используете фабрики с внешними URL (Faker)
                if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                    return $this->image;
                }

                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk('public');

                if ($disk->exists($this->image)) {
                    return $disk->url($this->image);
                }

                return asset('images/product-image.png');
            },
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withPivot('price', 'term');
    }
}
