<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\SellerController;


Route::post('/login', [AuthenticateController::class, 'authenticate'])->name('login');
Route::post('/logout', [AuthenticateController::class, 'logout']);

Route::post('/new-user', [UserController::class, 'store'])->name('user.store');


// Com sanctum
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/become-a-seller', [SellerController::class, 'becomeASeller'])->name('seller.become_a_seller');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
});
