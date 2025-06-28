<?php

namespace App\Http\Controllers\Seller;

use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\Product\ProductSellerResource;
use App\Http\Resources\Attribute\AttributeSummaryResource;
use App\Http\Resources\Attribute\AttributeWithOptionResource;

class ProductController extends Controller
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(StoreProductRequest $request)
    {
        $user= Auth::user();
        
        $product = $this->productService->storeWithAttributes($request->validated(), $user);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'product' => new ProductSellerResource($product)
        ], Response::HTTP_CREATED);
    }

    /**
     * Detalha o produto
     */
    public function detail(string $id)
    {
        $product = $this->productService->detail($id, Auth::user());

        return response()->json([
            'product' => new ProductSellerResource($product)
        ], Response::HTTP_OK);

    }

    public function getAttributesFromCategory(string $id)
    {

        $category = Category::findOrFail($id);

        $attributes = Attribute::with('attributeOptions')
                        ->where('category_id', $category->id)
                        ->get();
        
        
        return response()->json([
            'attributes' => AttributeWithOptionResource::collection($attributes)
        ], Response::HTTP_OK);
    }
}
