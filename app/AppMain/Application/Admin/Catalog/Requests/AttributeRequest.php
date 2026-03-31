<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('attribute') ?? $this->route('id');
        $isUpdate = $id !== null;

        return [
            'code' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'code')->ignore($id)
            ],
            'admin_name' => 'required|string|max:255',
            'type' => 'required|string|in:text,textarea,boolean,integer,select,multiselect,datetime,date,checkbox',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_filterable' => 'boolean',
            'is_configurable' => 'boolean',
            'options' => 'nullable|array',
            'options.*.admin_name' => 'required|string',
        ];
    }
}
