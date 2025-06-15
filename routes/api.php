<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

Route::get('/test', function () {
    $service = new ProductService();

    $payload = [
        'store_id' => 2,
        'name' => 'Computador gamer i9-12700H GTX9',
        'description' => 'Jogue GTA VI e muito mais!',
        'price' => 7850.56,
        'stock_quantity' => 15,
        'is_active' => true,
        'category_id' => 2,

        'attributes' => [
            [
                'attribute_id' => 2,    // Tipo tecido
                'attribute_option_id' => 7, // Algodao
            ],
            [
                'attribute_id' => 3,    // Tipo tecido
                'attribute_option_id' => 9, // Algodao
            ],
            // [
            //     'attribute_id' => 2,
            //     'attribute_option_id' => 5,
            // ],
            // [
            //     'attribute_id' => 3,
            //     'attribute_option_id' => 7,
            // ],
        ],

    ];

    $product = $service->store($payload);

    return response()->json([
        'product' => $product
    ], Response::HTTP_CREATED);
    
});