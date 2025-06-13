<?php

use App\Http\Controllers\ProdutoController;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $service = new ProductService();

    $payload = [
        'store_id' => 2,
        'name' => 'Blusa Preta Lisa WorkHard 3',
        'description' => 'Alguma descricao test...',
        'price' => 79.90,
        'stock_quantity' => 200,
        'is_active' => true,
        'category_id' => 1,

        'attributes' => [
            [
                'attribute_id' => 1,    // Tipo tecido
                'attribute_option_id' => 1, // Algodao
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

    $service->store($payload);
});