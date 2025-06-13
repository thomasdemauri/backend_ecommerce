<?php

namespace App\Services;

use App\Exceptions\AttributeDoesNotExistsForCategory;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use App\Exceptions\RequiredAttributesMissing;
use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService 
{

    public function store(array $payload)
    {

        DB::beginTransaction();

        try {

            $category = Category::with('attributes')->findOrFail($payload['category_id']);
            $attributes = $category->attributes;

            $product = Product::create([
                'store_id' => $payload['store_id'],
                'name' => $payload['name'],
                'slug' => Str::slug($payload['name'] . '-' . $payload['store_id']),
                'description' => $payload['description'],
                'price' => $payload['price'],
                'sku' => $payload['sku'] ?? '',
                'stock_quantity' => $payload['stock_quantity'],
                'is_active' => $payload['is_active'],
                'category_id' => $payload['category_id'],
            ]);
    
            // Obtem todos os atributos obrigatorio para verificar mais tarde se foi enviado no payload todos.
            $requiredAttributesIds = $attributes->where('required', true)->pluck('id')->toArray();

            $attributesIdsFromRequest = [];
            
            foreach ($payload['attributes'] as $attributeRequest) {
                
                $attributesIdsFromRequest[] = $attributeRequest['attribute_id'];
                
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
            
            $checkRequiredAttributes = array_diff($requiredAttributesIds, $attributesIdsFromRequest);
            
            // Se tiver itens aqui, significa que atributos requiridos estao faltando.
            if (!empty($checkRequiredAttributes)) {
                $missingIds = implode(',', $checkRequiredAttributes);
                throw new RequiredAttributesMissing($missingIds);
            }
            
            DB::commit();
            
            return $product->load('productAttributeValues.attributeOption.attribute');

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }


    }

}