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
        if (Schema::hasTable('price_rules') && !Schema::hasColumn('price_rules', 'markup_percent')) {
            Schema::table('price_rules', function (Blueprint $table) {
                $table->integer('markup_percent')->nullable()->default(0)->after('markup_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('price_rules') && Schema::hasColumn('price_rules', 'markup_percent')) {
            Schema::table('price_rules', function (Blueprint $table) {
                $table->dropColumn('markup_percent');
            });
        }
    }
};
