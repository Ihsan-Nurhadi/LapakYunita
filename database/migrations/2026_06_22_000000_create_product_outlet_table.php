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
        // 1. Create the pivot table
        Schema::create('product_outlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'outlet_id']);
        });

        // 2. Migrate existing product stock and outlet relations
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            if ($product->outlet_id) {
                // Ensure the outlet exists before inserting
                $outletExists = DB::table('outlets')->where('id', $product->outlet_id)->exists();
                if ($outletExists) {
                    DB::table('product_outlet')->insert([
                        'product_id' => $product->id,
                        'outlet_id' => $product->outlet_id,
                        'stock' => $product->stock,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Delete existing transaction history (as permitted by the user)
        DB::table('transaction_items')->delete();
        DB::table('pos_transactions')->delete();

        // 4. Drop stock and outlet_id columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'outlet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('category');
            $table->unsignedBigInteger('outlet_id')->nullable()->after('image');
        });

        // Restore data from product_outlet table to products table if possible
        $pivots = DB::table('product_outlet')->get();
        foreach ($pivots as $pivot) {
            DB::table('products')
                ->where('id', $pivot->product_id)
                ->update([
                    'stock' => $pivot->stock,
                    'outlet_id' => $pivot->outlet_id,
                ]);
        }

        Schema::dropIfExists('product_outlet');
    }
};
