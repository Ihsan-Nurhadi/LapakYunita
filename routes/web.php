<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return Redirect::to('/pos');
});

use App\Http\Controllers\PosController;

Route::get('/pos', [PosController::class, 'index']);

Route::get('/run-migration-aiven', function () {
    try {
        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        if ($driver === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE products MODIFY image LONGTEXT NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE employees MODIFY photo LONGTEXT NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE outlets MODIFY image LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE products ALTER COLUMN image TYPE TEXT');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE employees ALTER COLUMN photo TYPE TEXT');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE outlets ALTER COLUMN image TYPE TEXT');
        }
        return response()->json(['success' => true, 'message' => 'Migration Aiven Berhasil! Tipe kolom gambar telah diubah ke LONGTEXT/TEXT.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/run-migration-price-rules', function () {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('price_rules')) {
            \Illuminate\Support\Facades\Schema::create('price_rules', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->integer('min_weight')->default(0);
                $table->integer('max_weight')->default(0);
                $table->integer('markup_price')->default(0);
                $table->timestamps();
            });

            \Illuminate\Support\Facades\DB::table('price_rules')->insert([
                ['min_weight' => 1, 'max_weight' => 500, 'markup_price' => 12000, 'created_at' => now(), 'updated_at' => now()],
                ['min_weight' => 501, 'max_weight' => 900, 'markup_price' => 16500, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('products', 'weight')) {
            \Illuminate\Support\Facades\Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('weight')->nullable()->default(0);
            });
        }

        return response()->json(['success' => true, 'message' => 'Migration Aiven Price Rules Berhasil! Tabel price_rules & kolom weight telah ditambahkan.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/compress-existing-images', function () {
    try {
        $controller = new \App\Http\Controllers\PosController();
        $updatedProducts = 0;
        $products = \App\Models\Product::where('image', 'LIKE', 'data:image/%')->get();
        foreach ($products as $p) {
            $oldLen = strlen($p->image);
            if ($oldLen > 30000) {
                $newImage = $controller->compressDataUrl($p->image);
                if ($newImage && strlen($newImage) < $oldLen) {
                    $p->image = $newImage;
                    $p->save();
                    $updatedProducts++;
                }
            }
        }

        $updatedEmployees = 0;
        $employees = \App\Models\Employee::where('photo', 'LIKE', 'data:image/%')->get();
        foreach ($employees as $e) {
            $oldLen = strlen($e->photo);
            if ($oldLen > 30000) {
                $newPhoto = $controller->compressDataUrl($e->photo);
                if ($newPhoto && strlen($newPhoto) < $oldLen) {
                    $e->photo = $newPhoto;
                    $e->save();
                    $updatedEmployees++;
                }
            }
        }

        $updatedOutlets = 0;
        $outlets = \App\Models\Outlet::where('image', 'LIKE', 'data:image/%')->get();
        foreach ($outlets as $o) {
            $oldLen = strlen($o->image);
            if ($oldLen > 30000) {
                $newImage = $controller->compressDataUrl($o->image);
                if ($newImage && strlen($newImage) < $oldLen) {
                    $o->image = $newImage;
                    $o->save();
                    $updatedOutlets++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Kompresi selesai! Produk terkompresi: {$updatedProducts}, Pegawai: {$updatedEmployees}, Outlet: {$updatedOutlets}."
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::prefix('pos/api')->group(function(){
    Route::post('login', [PosController::class, 'login']);
    Route::post('logout', [PosController::class, 'logout']);
    Route::get('me', [PosController::class, 'me']);
    Route::get('employees', [PosController::class, 'employees']);
    Route::post('change-pin', [PosController::class, 'changePin']);
    Route::get('products/{product}/image', [PosController::class, 'getProductImage']);
    Route::get('employees/{employee}/photo', [PosController::class, 'getEmployeePhoto']);
    Route::get('outlets/{outlet}/image', [PosController::class, 'getOutletImage']);
});

Route::prefix('pos/api')->middleware('employee.auth')->group(function(){
    Route::get('products', [PosController::class, 'products']);
    Route::post('products', [PosController::class, 'storeProduct'])->middleware('employee.auth:admin');
    Route::put('products/{product}', [PosController::class, 'updateProduct'])->middleware('employee.auth:admin');
    Route::delete('products/{product}', [PosController::class, 'deleteProduct'])->middleware('employee.auth:admin');

    Route::post('employees', [PosController::class, 'storeEmployee'])->middleware('employee.auth:admin');
    Route::put('employees/{employee}', [PosController::class, 'updateEmployee'])->middleware('employee.auth:admin');
    Route::delete('employees/{employee}', [PosController::class, 'deleteEmployee'])->middleware('employee.auth:admin');

    Route::get('discount-rule', [PosController::class, 'getDiscountRule']);
    Route::post('discount-rule', [PosController::class, 'saveDiscountRule'])->middleware('employee.auth:admin');

    Route::get('customers', [PosController::class, 'customers']);
    Route::post('customers', [PosController::class, 'storeCustomer']);
    Route::put('customers/{customer}', [PosController::class, 'updateCustomer']);
    Route::delete('customers/{customer}', [PosController::class, 'destroyCustomer']);
    Route::get('customer-tiers', [PosController::class, 'customerTiers']);
    Route::post('customer-tiers', [PosController::class, 'saveCustomerTiers'])->middleware('employee.auth:admin');

    Route::get('price-rules', [PosController::class, 'priceRules']);
    Route::post('price-rules', [PosController::class, 'savePriceRules'])->middleware('employee.auth:admin');
    Route::delete('price-rules/{priceRule}', [PosController::class, 'deletePriceRule'])->middleware('employee.auth:admin');

    Route::get('outlets', [PosController::class, 'outlets']);
    Route::post('outlets', [PosController::class, 'storeOutlet'])->middleware('employee.auth:admin');
    Route::put('outlets/{outlet}', [PosController::class, 'updateOutlet'])->middleware('employee.auth:admin');
    Route::delete('outlets/{outlet}', [PosController::class, 'deleteOutlet'])->middleware('employee.auth:admin');

    Route::get('drafts', [PosController::class, 'drafts']);
    Route::post('drafts', [PosController::class, 'storeDraft']);
    Route::delete('drafts/{draft}', [PosController::class, 'deleteDraft']);
    Route::get('transactions', [PosController::class, 'transactions']);
    Route::post('transactions', [PosController::class, 'storeTransaction']);
});
