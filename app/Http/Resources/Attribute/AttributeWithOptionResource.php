<?php

namespace App\Http\Resources\Attribute;

use App\Http\Resources\AttributeOption\AttributeOptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeWithOptionResource extends JsonResource
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
            'name' => $this->name,
            'options' => AttributeOptionResource::collection($this->whenLoaded('attributeOptions'))
        ];
    }
}
