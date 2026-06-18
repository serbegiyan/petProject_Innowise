<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'price' => $this->faker->numberBetween(10, 2000),
            'brand' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'image' => null,
            'release_date' => $this->faker->date(),
        ];
    }

    public function withCategories(int $count = 1): static
    {
        return $this->hasAttached(Category::factory()->count($count));
    }

    public function withServices(int $count = 1): static
    {
        return $this->hasAttached(Service::factory()->count($count), [
            'price' => $this->faker->numberBetween(10, 200),
            'term' => $this->faker->numberBetween(1, 30),
        ]);
    }
}
