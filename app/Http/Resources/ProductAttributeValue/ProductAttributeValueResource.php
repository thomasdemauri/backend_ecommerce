<?php

namespace App\Http\Resources\ProductAttributeValue;

use App\Http\Resources\Attribute\AttributeSummaryResource;
use App\Http\Resources\AttributeOption\AttributeOptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'attribute' => new AttributeSummaryResource($this->whenLoaded('attribute')),
            'option' => new AttributeOptionResource($this->whenLoaded('attributeOption'))
        ];
    }
}
