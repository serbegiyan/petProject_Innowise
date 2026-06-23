<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 3)->unique();
            $table->integer('scale')->unsigned();
            $table->decimal('rate', 10, 4);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE exchange_rates ADD CONSTRAINT chk_exchange_rates_scale_positive CHECK (scale > 0)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
