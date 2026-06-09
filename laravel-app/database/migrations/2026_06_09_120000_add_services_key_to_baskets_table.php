<?php

use App\Models\Basket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->string('services_key', 255)->default('[]')->after('services');
        });

        Basket::query()->each(function (Basket $basket): void {
            $basket->update(['services_key' => Basket::servicesKey($basket->services)]);
        });

        Schema::table('baskets', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'services_key']);
        });
    }

    public function down(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id', 'services_key']);
            $table->dropColumn('services_key');
        });
    }
};
