<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user && $user->is_seller;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product' => ['required', 'array'],

            'product.name' => ['required', 'max:150', 'string'],
            'product.description' => ['required'],
            'product.price' => ['required', 'decimal:2'],
            'product.sku' => ['nullable', 'max:50', 'string'],
            'product.stock_quantity' => ['integer', 'min:0', 'sometimes'],
            'product.is_active' => ['sometimes', 'boolean'],
            'product.category_id' => ['required', 'exists:categories,id'],
            'product.weight' => ['nullable'],
            'product.length' => ['nullable'],
            'product.width' => ['nullable'],
            'product.height' => ['nullable'],

            'attributes' => ['required', 'array', 'min:1'],
            'attributes.*.attribute_id' => ['required', 'exists:attributes,id'],
            'attributes.*.attribute_option' => ['required', 'exists:attribute_options,id'],
        ];
    }
}
