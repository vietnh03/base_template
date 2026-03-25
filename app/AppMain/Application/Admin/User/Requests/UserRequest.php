<?php

namespace App\AppMain\Application\Admin\User\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Works for both create and update
     */
    public function rules(): array
    {
        $userId = $this->route('id'); // null for create, has value for update
        $isUpdate = $userId !== null;

        return [
            // Required for create, optional for update
            'name' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id')
            ],
            'password' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|min:8',

            // Optional fields
            'role' => 'nullable|in:admin,manager,user',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'role.in' => 'Role must be admin, manager, or user',
            'status.in' => 'Status must be active, inactive, or suspended',
        ];
    }
}
