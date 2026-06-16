<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'services_key'], 'baskets_user_product_services_unique');
        });
    }

    public function down(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            $table->dropUnique('baskets_user_product_services_unique');
        });
    }
};
