<?php

namespace App\Http\Resources\AttributeOption;

use App\Http\Resources\Attribute\AttributeSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeOptionResource extends JsonResource
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
            'label' => $this->label,
            'value' => $this->value,
            'attribute' => new AttributeSummaryResource($this->whenLoaded('attribute'))
        ];
    }
}
