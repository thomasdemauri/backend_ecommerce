<?php

use App\Http\Controllers\ProdutoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/v1')->group(function () {
    // Route::apiResource('produto', ProdutoController::class);
    Route::get('/produtos', [ProdutoController::class, 'index'])->name('produto.index');
    Route::get('/produto/{id}', [ProdutoController::class, 'show'])->name('produto.show');
    Route::post('/produto', [ProdutoController::class, 'store'])->name('produto.store');
});

Route::get('/', function () {
    return response()->json("Hello world");
});