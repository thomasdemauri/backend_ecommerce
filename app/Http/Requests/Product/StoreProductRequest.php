<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->is_seller;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:150', 'string'],
            'description' => ['required'],
            'price' => ['required', 'decimal'],
            'sku' => ['nullable', 'max:50', 'string'],
            'stock_quantity' => ['number', 'min:0'],
            'category_id' => ['required', 'exists:category,id'],
            'weight' => ['nullable'],
            'length' => ['nullable'],
            'width' => ['nullable'],
            'height' => ['nullable'],
        ];
    }
}
