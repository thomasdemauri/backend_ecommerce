<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Auth;

Route::post('/login', [AuthenticateController::class, 'authenticate'])->name('login');
Route::post('/logout', [AuthenticateController::class, 'logout']);

Route::post('/new-user', [UserController::class, 'store'])->name('user.store');


// Com sanctum
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/seller/become', [SellerController::class, 'createSellerWithStore'])->name('seller.create_seller_with_store');
    Route::post('/seller/product', [ProductController::class, 'store'])->name('product.store');
});

Route::middleware('auth:sanctum')->get('/me', function () {
    return response()->json([
        'user' => Auth::user()
    ]);
});
