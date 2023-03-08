<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'authenticateUser']);
Route::middleware(['jwt'])->group(function () {
    //Item
    Route::get('productType/{productTypeId}/items', [ItemController::class, 'index']);
    Route::post('productType/{productTypeId}/item', [ItemController::class, 'store']);
    Route::post('productType/{productTypeId}/item/update', [ItemController::class, 'update']);
    Route::delete('productType/{productId}/item/{itemId}', [ItemController::class, 'delete']);
    //ProductType
    Route::get('productTypes', [ProductTypeController::class, 'index']);
    Route::post('productType', [ProductTypeController::class, 'store']);
    Route::post('productType/update', [ProductTypeController::class, 'update']);
    Route::delete('productType/{productTypeId}', [ProductTypeController::class, 'delete']);

});
