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

Route::prefix('pos/api')->group(function(){
    Route::post('login', [PosController::class, 'login']);
    Route::post('logout', [PosController::class, 'logout']);
    Route::get('me', [PosController::class, 'me']);
    Route::get('employees', [PosController::class, 'employees']);
    Route::post('change-pin', [PosController::class, 'changePin']);
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
