<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class AttributeFamilyRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('family') ?? $this->route('id');
        $isUpdate = $id !== null;

        return [
            'code' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('attribute_families', 'code')->ignore($id)
            ],
            'name' => 'required|string|max:255',
            'status' => 'boolean',
            'groups' => 'nullable|array',
        ];
    }
}
