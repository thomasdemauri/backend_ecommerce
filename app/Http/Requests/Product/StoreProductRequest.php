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
            'name' => ['required', 'max:150', 'string'],
            'description' => ['required'],
            'price' => ['required', 'decimal:2'],
            'sku' => ['nullable', 'max:50', 'string'],
            'stock_quantity' => ['integer', 'min:0', 'nullable'],
            'category_id' => ['required', 'exists:categories,id'],
            'weight' => ['nullable'],
            'length' => ['nullable'],
            'width' => ['nullable'],
            'height' => ['nullable'],
            'attributes' => ['required', 'array', 'min:1'],
        ];
    }
}
