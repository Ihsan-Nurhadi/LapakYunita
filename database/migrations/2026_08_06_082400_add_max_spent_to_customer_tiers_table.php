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
        Schema::table('customer_tiers', function (Blueprint $table) {
            $table->integer('max_spent')->nullable()->after('min_spent');
        });

        // Set initial range limits for seeded tiers
        DB::table('customer_tiers')->where('name', 'Silver')->update(['max_spent' => 499999]);
        DB::table('customer_tiers')->where('name', 'Gold')->update(['max_spent' => 1999999]);
        DB::table('customer_tiers')->where('name', 'Platinum')->update(['max_spent' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_tiers', function (Blueprint $table) {
            $table->dropColumn('max_spent');
        });
    }
};
