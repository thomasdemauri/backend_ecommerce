<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\RequiredAttributesMissing;
use App\Http\Resources\Product\ProductResource;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use App\Exceptions\AttributeDoesNotExistsForCategory;
use App\Http\Resources\Product\ProductSellerResource;

class ProductService
{

    private StoreService $storeService;
    private SellerService $sellerService;

    public function __construct(StoreService $storeService, SellerService $sellerService)
    {
        $this->storeService = $storeService;
        $this->sellerService = $sellerService;
    }

    public function detail(string $id)
    {
        $product = Product::findOrFail($id);
    }

    /**
     * Cria produto com seus devidos atributos.
     * 
     * @param array $payload Contem os dados do produto
     * e de seus atributos com suas respectivas opções.
     * 
     * @throws AttributeDoesNotExistsForCategory Lança exceção quando atributo não corresponder a categoria.
     * 
     * @throws OptionDoesNotExistsForAttribute Lança exceção quando opção não corresponder a atributo.
     * 
     * @throws RequiredAttributesMissing Lança exceção quando estiver faltando 
     * atributo requerido no corpo da requisição.
     * 
     */
    public function store(array $payload)
    {

        DB::beginTransaction();

        $seller = User::with('store')->findOrFail(Auth::user()->id);
        // dd($seller->store->id);
        try {
            
            $category = Category::with('attributes')->findOrFail($payload['category_id']);
            $attributes = $category->attributes;

            $product = Product::create([
                'store_id' => $seller->store->id,
                'name' => $payload['name'],
                'slug' => Str::slug($payload['name'] . '-' . $seller->store->id),
                'description' => $payload['description'],
                'price' => $payload['price'],
                'sku' => $payload['sku'] ?? '',
                'stock_quantity' => $payload['stock_quantity'],
                'category_id' => $payload['category_id'],
            ]);
            
            foreach ($payload['attributes'] as $attributeRequest) {
                                
                $attribute = $attributes->firstWhere('id', $attributeRequest['attribute_id']);
                
                if (!$attribute) {
                    throw new AttributeDoesNotExistsForCategory($attributeRequest['attribute_id']);
                }

                $selectedOption = $attribute->attributeOptions->firstWhere('id', $attributeRequest['attribute_option_id']);
                
                if (!$selectedOption) {
                    throw new OptionDoesNotExistsForAttribute($attributeRequest['attribute_option_id'], $attribute->id);
                }
                
                $product->productAttributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'attribute_option_id' => $selectedOption->id,
                    'value' => $selectedOption->value
                ]);

            }
            
            $requiredAttributesIds = $attributes->where('required', true)->pluck('id')->toArray();
            $this->checkRequiredAttributesInPayload($payload['attributes'], $requiredAttributesIds);
            
            DB::commit();
            
            $product->load('productAttributeValues.attributeOption', 'productAttributeValues.attribute', 'store');
            $product->setRelation('category', $category);   // Evita carregar novamente

            return new ProductSellerResource($product);

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

    }

    /**
     * Verifica os IDs requeridos e compara com os ids presente no payload.
     * 
     * Caso esteja faltando algum lança exceção, senão retorna false.
     * 
     * @param array $attributesPayload Array contendo os atributos do payload.
     * @param array $requiredAttributes Array contendo os atributos requeridos.
     * 
     * @return false Caso nenhum atributo requerido esteja faltando.
     * 
     * @throws RequiredAttributesMissing Quando estiver faltando atributos requeridos.
     */
    private function checkRequiredAttributesInPayload(array $attributesPayload, array $requiredAttributes)
    {
        $attributesIdsFromPayload = array_map(function ($attribute) {
            return $attribute['attribute_id'];
        }, $attributesPayload);

        $missingRequiredAttributeIds = array_diff($requiredAttributes, $attributesIdsFromPayload);

        if (!empty($missingRequiredAttributeIds)) {
            $ids = implode(',', $missingRequiredAttributeIds);
            throw new RequiredAttributesMissing($ids);
        }

        return false;
    }   

}