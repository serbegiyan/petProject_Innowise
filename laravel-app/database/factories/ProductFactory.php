<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Service;

class ProductFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'price' => $this->faker->numberBetween(10, 2000),
            'brand' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(600, 600, 'products', true),
            'release_date' => $this->faker->date(),
        ];
    }

    public function withCategories(int $count = 1)
    {
        return $this->hasAttached(Category::factory()->count($count));
    }

    public function withServices(int $count = 1)
    {
        return $this->hasAttached(Service::factory()->count($count), [
            'price' => $this->faker->numberBetween(10, 200),
            'term' => $this->faker->numberBetween(1, 30),
        ]);
    }
}
