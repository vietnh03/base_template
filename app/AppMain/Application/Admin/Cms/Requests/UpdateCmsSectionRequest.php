<?php

namespace App\AppMain\Application\Admin\Cms\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCmsSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'locale' => 'required|string|in:vi,en',
            'content' => 'required|array',
        ];
    }
}
