<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\products\ProductController;

Route::resource('products', ProductController::class);
