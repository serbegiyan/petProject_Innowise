<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AssignDefaultProductImages extends Command
{
    protected $signature = 'products:assign-default-images';

    protected $description = 'Assign default product image to catalog items without an image';

    public function handle(): int
    {
        $defaultImage = 'product-image.png';

        if (! Storage::disk('public')->exists($defaultImage)) {
            $source = database_path('seeders/assets/'.$defaultImage);

            if (! file_exists($source)) {
                $this->error('Default image not found in storage or seeders/assets.');

                return self::FAILURE;
            }

            Storage::disk('public')->put($defaultImage, file_get_contents($source));
        }

        $updated = Product::whereNull('image')->update(['image' => $defaultImage]);

        $this->info("Updated {$updated} product(s) with default image.");

        return self::SUCCESS;
    }
}
