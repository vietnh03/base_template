<?php

namespace App\AppMain\Application\Admin\Post\Requests;

use App\AppMain\Core\BaseFormRequest;

class PostRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'status' => 'boolean',
            'image' => 'sometimes|image|max:2048',
            'author_id' => 'nullable|uuid|exists:admins,id',
            'published_at' => 'nullable|date',
            'categories' => 'array',
            'categories.*' => 'uuid|exists:post_categories,id',
            'tags' => 'array',
            'tags.*' => 'uuid|exists:post_tags,id',
            'translations' => 'required|array|min:1',
        ];

        foreach (config('app.locales', ['en', 'vi']) as $locale) {
            $rules["translations.{$locale}.name"] = 'required_with:translations.' . $locale . '|string|max:255';
            $rules["translations.{$locale}.slug"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.short_description"] = 'nullable|string';
            $rules["translations.{$locale}.content"] = 'required_with:translations.' . $locale . '|string';
            $rules["translations.{$locale}.meta_title"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.meta_keywords"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.meta_description"] = 'nullable|string';
        }

        return $rules;
    }
}
