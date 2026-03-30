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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('price', 8, 2)->default(0)->index();
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->date('release_date')->nullable()->index();

            // indexes
            $table->index(['brand', 'price']);
            $table->index(['deleted_at', 'price']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
