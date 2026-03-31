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
            'name' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')->ignore($id, 'id')
            ],
        ];
    }
}
