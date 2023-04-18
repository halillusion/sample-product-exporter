<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductExporterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(ProductExporterController::class)->group(function () {
    Route::get('/', 'api')->name('apiHome');
    Route::get('/products', 'products')->name('productList');
    Route::get('/products/export', 'exportProducts')->name('exportProducts');
});