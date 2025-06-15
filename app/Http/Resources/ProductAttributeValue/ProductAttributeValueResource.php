<?php

namespace App\Http\Resources\ProductAttributeValue;

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
            'id' => $this->id,
            'value' => $this->value,
            'option' => new AttributeOptionResource($this->whenLoaded('attributeOption'))
        ];
    }
}
