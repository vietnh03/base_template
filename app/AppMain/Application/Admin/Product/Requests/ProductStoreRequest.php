<?php

namespace App\AppMain\Application\Admin\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:255|unique:products,sku',
            'type' => 'required|string',
            'status' => 'nullable|boolean',
            'cost_price' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|integer|exists:products,id',
            'attribute_family_id' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'integer', // intentionally skipping exists:categories,id check for perf as per user preference on constraints
            'tags' => 'nullable|array',
            'tags.*' => 'integer',
            'attributes' => 'nullable|array',
            'locale' => 'nullable|string',
        ];
    }
}
