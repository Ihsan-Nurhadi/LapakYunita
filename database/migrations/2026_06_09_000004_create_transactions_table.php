<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id')->unique();
            $table->integer('total')->default(0);
            $table->integer('paid')->default(0);
            $table->integer('change')->default(0);
            $table->string('cashier')->nullable();
            $table->string('outlet')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
