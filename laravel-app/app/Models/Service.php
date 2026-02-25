<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model
{
    use HasSlug;
    protected $table = 'services';
    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

  /**
     * Настройки генерации слага
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name') 
            ->saveSlugsTo('slug')       
            ->doNotGenerateSlugsOnUpdate(); 
    }

    /**
     * Позволяет Laravel искать модель по слагу в URL автоматически
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
