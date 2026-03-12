<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $services = Service::all();

        $data = [
            'Смартфоны' => [['Apple iPhone 15 Pro', 'Apple', 3600], ['Samsung Galaxy S24 Ultra', 'Samsung', 4200], ['Xiaomi 14 Ultra', 'Xiaomi', 3800], ['Google Pixel 8 Pro', 'Google', 3100], ['Asus ROG Phone 8', 'Asus', 3500], ['OnePlus 12', 'OnePlus', 2900], ['Huawei Pura 70 Pro', 'Huawei', 3300], ['Nothing Phone (2)', 'Nothing', 2100], ['Sony Xperia 1 V', 'Sony', 3400], ['Realme GT 6', 'Realme', 1800]],
            'Ноутбуки' => [['MacBook Air M3 13"', 'Apple', 4100], ['ASUS ROG Zephyrus G14', 'Asus', 5200], ['Lenovo Legion Slim 5', 'Lenovo', 3800], ['HP Spectre x360', 'HP', 4500], ['Dell XPS 13 9340', 'Dell', 4800], ['Acer Swift Go 14', 'Acer', 2600], ['MSI Katana 15', 'MSI', 3200], ['Huawei MateBook X Pro', 'Huawei', 4900], ['Gigabyte Aorus 15', 'Gigabyte', 4300], ['Samsung Galaxy Book4 Pro', 'Samsung', 4600]],
            'Планшеты' => [['iPad Pro 11" M4', 'Apple', 3900], ['Samsung Galaxy Tab S9 FE', 'Samsung', 1500], ['Xiaomi Pad 6', 'Xiaomi', 1200], ['Huawei MatePad Pro 13.2', 'Huawei', 2800], ['Lenovo Tab P12', 'Lenovo', 1400], ['Google Pixel Tablet', 'Google', 1900], ['OnePlus Pad', 'OnePlus', 1700], ['Microsoft Surface Pro 9', 'Microsoft', 3500], ['Honor Pad 9', 'Honor', 1100], ['iPad mini 6', 'Apple', 1800]],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::firstOrCreate([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            foreach ($products as $item) {
                $product = Product::create([
                    'name' => $item[0],
                    'slug' => Str::slug($item[0]),
                    'brand' => $item[1],
                    'description' => "Отличный представитель категории {$categoryName} от бренда {$item[1]}.",
                    'price' => $item[2],
                    'release_date' => now()->subMonths(rand(1, 12)),
                    'image' => 'product-image.png',
                ]);

                $product->categories()->attach($category->id);

                $randomServices = $services->random(rand(1, 4));

                foreach ($randomServices as $service) {
                    $product->services()->attach($service->id, [
                        'price' => rand(20, 150),
                        'term' => rand(1, 7).' дней',
                    ]);
                }
            }
        }
    }
}
