<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategorySummaryResource;
use App\Http\Resources\ProductAttributeValue\ProductAttributeValueResource;
use App\Http\Resources\Store\StoreResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSellerResource extends JsonResource
{

    /**
     * Resource responsável por ser mostrado no painel do vendedor
     */

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stockQuantity' => $this->stock_quantity,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'sku'=> $this->sku,
            'isActive' => $this->is_active,

            
            // Relations
            'category' => new CategorySummaryResource($this->whenLoaded('category')),
            'store' => new StoreResource($this->whenLoaded('store')),
            'attributes' => ProductAttributeValueResource::collection($this->whenLoaded('productAttributeValues'))
        ];
    }
}
