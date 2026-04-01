<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');
        $isUpdate = $id !== null;

        return [
            'parent_id' => 'nullable|exists:categories,id',
            'position' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'additional' => 'nullable|array',
            'translations' => ($isUpdate ? 'sometimes|' : 'required|') . 'array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.url_key' => 'nullable|string|max:255',
            'translations.*.meta_title' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string',
            'translations.*.meta_description' => 'nullable|string',
        ];
    }
}
