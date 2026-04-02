<?php

namespace App\AppMain\Application\Admin\User\Requests;

use App\AppMain\Core\BaseFormRequest;

class UserRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user');
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|unique:users,phone,' . $id,
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'status' => 'nullable|integer',
            'password' => $this->isMethod('POST') ? 'required|min:8' : 'nullable|min:8',
            'notes' => 'nullable|string',
        ];
    }
}
