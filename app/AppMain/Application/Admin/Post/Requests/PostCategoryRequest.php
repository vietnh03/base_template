<?php

namespace App\AppMain\Application\Admin\Post\Requests;

use App\AppMain\Core\BaseFormRequest;

class PostCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'parent_id' => 'nullable|uuid|exists:post_categories,id',
            'status' => 'boolean',
            'position' => 'integer|min:0',
            'image' => 'sometimes|image|max:2048',
            'translations' => 'required|array|min:1',
        ];

        foreach (config('app.locales', ['en', 'vi']) as $locale) {
            $rules["translations.{$locale}.name"] = 'required_with:translations.' . $locale . '|string|max:255';
            $rules["translations.{$locale}.slug"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.description"] = 'nullable|string';
            $rules["translations.{$locale}.meta_title"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.meta_keywords"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.meta_description"] = 'nullable|string';
        }

        return $rules;
    }
}
