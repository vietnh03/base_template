<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');
        $isUpdate = $id !== null;
        $locales = \App\Models\Locale::pluck('code')->toArray();
        $localeRule = Rule::in($locales);

        return [
            'sku' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($id, 'id')
            ],
            'type' => 'nullable|string',
            'status' => 'nullable|boolean',
            'cost_price' => 'nullable|numeric',
            'attribute_family_id' => 'sometimes|exists:attribute_families,id',
            'weight' => 'nullable|numeric',
            'thumbnail' => 'nullable|string',
            'new' => 'nullable|boolean',
            'featured' => 'nullable|boolean',

            // Flat data validation
            'flat' => ($isUpdate ? 'sometimes|' : 'required|') . 'array',
            'flat.*' => 'array',
            // We can check keys in a custom validation if needed, but for now we validate the content
            'flat.*.name' => 'required|string|max:255',
            'flat.*.description' => 'nullable|string',
            'flat.*.price' => 'required|numeric',
            'flat.*.url_key' => 'nullable|string|max:255',
            'flat.*.meta_title' => 'nullable|string',
            'flat.*.meta_keywords' => 'nullable|string',
            'flat.*.meta_description' => 'nullable|string',

            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',

            'inventories' => 'nullable|array',
            'inventories.*.id' => 'sometimes|exists:product_inventories,id',
            'inventories.*.qty' => 'required|integer|min:0',

            'images' => 'nullable|array',
            'images.*.id' => 'sometimes|exists:product_images,id',
            'images.*.path' => 'required|string',
            'images.*.type' => 'nullable|string',
            'images.*.position' => 'nullable|integer',

            'attribute_values' => 'nullable|array',
            'attribute_values.*' => 'nullable', // Generic because type depends on attribute

            'up_sells' => 'nullable|array',
            'up_sells.*' => 'exists:products,id',
            'cross_sells' => 'nullable|array',
            'cross_sells.*' => 'exists:products,id',
            'super_attributes' => 'nullable|array',
            'super_attributes.*' => 'exists:attributes,id',
        ];
    }
}
