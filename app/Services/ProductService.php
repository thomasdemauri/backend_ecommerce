<?php

namespace App\Services;

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
    
            $category = Category::findOrFail($payload['category_id']);
    
            $attributes = $category->attributes;

            // Verifica se todos os atributos OBRIGATORIOS foram enviado com sucesso
            $requiredAttributesIds = $attributes->where('required', true)->pluck('id')->toArray();

            $attributesIdsFromRequest = [];

            foreach ($payload['attributes'] as $attributeRequest) {
                $attributesIdsFromRequest[] = $attributeRequest['attribute_id'];
            }
            
            $checkRequiredAttributes = array_diff($requiredAttributesIds, $attributesIdsFromRequest);

            // Se tiver itens aqui, significa que atributos requiridos estao faltando
            if (!empty($checkRequiredAttributes)) {
                throw new Exception('Required attributes are missing.');
            }
    
            foreach ($payload['attributes'] as $attributeRequest) {
    
                $attribute = $attributes->firstWhere('id', $attributeRequest['attribute_id']);

                if (!$attribute) {
                    throw new Exception('Attribute does not exist for this category [' . $category->name . ']');
                }
    
                
                $selectedOption = $attribute->attributeOptions->firstWhere('id', $attributeRequest['attribute_option_id']);

                if (!$selectedOption) {
                    throw new Exception('Option does not exist for this category [' . $category->name . ']');
                }
    
                $product->productAttributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'attribute_option_id' => $selectedOption->id,
                    'value' => $selectedOption->value
                ]);
            }
    
            DB::commit();
            
            return $product->load('productAttributeValues.attributeOption.attribute');

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }


    }

}