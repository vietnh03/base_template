<?php

namespace App\AppMain\Application\Admin\User\Requests;

use App\AppMain\Core\BaseFormRequest;

class UserFilter extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'status' => 'nullable|integer',
            'gender' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
