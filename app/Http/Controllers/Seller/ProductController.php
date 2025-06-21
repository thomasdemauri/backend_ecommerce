<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\Product\ProductSellerResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(StoreProductRequest $request)
    {
        $productData = $request->validated();
        
        $product = $this->productService->store($productData);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'product' => new ProductSellerResource($product)
        ], Response::HTTP_CREATED);
    }
}
