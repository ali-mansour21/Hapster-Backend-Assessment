<?php

use App\Http\Controllers\api\orders\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\products\ProductController;

Route::resource('products', ProductController::class);
Route::controller(OrderController::class)->group(function () {
    Route::get('orders', 'index');
    Route::get('orders/{order}', 'show');
    Route::post('orders', 'store');
});
