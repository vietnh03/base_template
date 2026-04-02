<?php

namespace App\AppMain\Application\Admin\Post\Requests;

use App\AppMain\Core\BaseFormRequest;

class PostTagRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'status' => 'boolean',
            'translations' => 'required|array|min:1',
        ];

        foreach (config('app.locales', ['en', 'vi']) as $locale) {
            $rules["translations.{$locale}.name"] = 'required_with:translations.' . $locale . '|string|max:255';
            $rules["translations.{$locale}.slug"] = 'nullable|string|max:255';
        }

        return $rules;
    }
}
