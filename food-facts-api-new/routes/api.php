<?php

use App\Application\Products\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API information
Route::get('/', [ProductController::class, 'index']);

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'listProducts']);
    Route::get('/{code}', [ProductController::class, 'getProduct']);
    Route::put('/{code}', [ProductController::class, 'updateProduct']);
    Route::delete('/{code}', [ProductController::class, 'deleteProduct']);
    Route::post('/', [ProductController::class, 'createProduct']);
});

// Import
Route::post('/import', [ProductController::class, 'importProducts']);