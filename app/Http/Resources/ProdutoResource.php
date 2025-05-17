<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
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
            'arquivo3d'=> $this->arquivo_3d,
            'capa' => $this->capa,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
        ];
    }
}
