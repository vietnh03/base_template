<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');
        $isUpdate = $id !== null;

        return [
            'status' => 'sometimes|boolean',
            'translations' => ($isUpdate ? 'sometimes|' : 'required|') . 'array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.slug' => 'sometimes|string|max:255',
        ];
    }
}
