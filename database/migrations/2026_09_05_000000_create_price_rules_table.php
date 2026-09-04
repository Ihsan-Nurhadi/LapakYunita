<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('min_weight')->default(0); // in grams
            $table->integer('max_weight')->default(0); // in grams
            $table->integer('markup_price')->default(0); // in Rupiah
            $table->timestamps();
        });

        // Seed initial rules
        DB::table('price_rules')->insert([
            [
                'min_weight' => 1,
                'max_weight' => 500,
                'markup_price' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'min_weight' => 501,
                'max_weight' => 900,
                'markup_price' => 16500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
    }
};
