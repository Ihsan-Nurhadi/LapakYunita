<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name');
            $table->integer('qty')->default(1);
            $table->integer('price')->default(0);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('pos_transactions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
