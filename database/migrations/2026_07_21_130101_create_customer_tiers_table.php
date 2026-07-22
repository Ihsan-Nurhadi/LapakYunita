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
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('badge');
            $table->integer('min_spent')->default(0);
            $table->integer('discount_percent')->default(0);
            $table->timestamps();
        });

        // Seed initial values
        DB::table('customer_tiers')->insert([
            [
                'name' => 'Silver',
                'badge' => '🥈',
                'min_spent' => 0,
                'discount_percent' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold',
                'badge' => '🥇',
                'min_spent' => 500000,
                'discount_percent' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Platinum',
                'badge' => '💎',
                'min_spent' => 2000000,
                'discount_percent' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_tiers');
    }
};
