<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'arquivo_3d'    => 'required|file',
            'capa'          => 'required',
            'titulo'        => 'required|string|max:64',
            'descricao'     => 'required|min:10',
            'valor'         => 'required|decimal:0,2'
        ];
    }
}
