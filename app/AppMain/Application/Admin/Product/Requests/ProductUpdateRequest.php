<?php

namespace App\AppMain\Application\Admin\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('product');
        return [
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
            'status' => 'nullable|boolean',
            'cost_price' => 'nullable|numeric|min:0',
            'categories' => 'nullable|array',
            'categories.*' => 'integer',
            'tags' => 'nullable|array',
            'tags.*' => 'integer',
            'attributes' => 'nullable|array',
            'locale' => 'nullable|string',
        ];
    }
}
