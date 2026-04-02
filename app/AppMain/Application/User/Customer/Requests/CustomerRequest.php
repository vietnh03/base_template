<?php

namespace App\AppMain\Application\User\Customer\Requests;

use App\AppMain\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Works for both create and update
     */
    public function rules(): array
    {
        $customerId = $this->route('id'); // null for create, has value for update
        $isUpdate = $customerId !== null;

        return [
            // Required for create, optional for update
            'name' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'phone' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:20',
                Rule::unique('customers', 'phone')->ignore($customerId, 'id')
            ],

            // Authentication and Status
            'password' => ($isUpdate ? 'sometimes|' : 'nullable|') . 'string|min:8',
            'status' => 'nullable|integer',
            'is_verified' => 'nullable|boolean',
            'token' => 'nullable|string|max:255',

            // Optional fields
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId, 'id')
            ],

            'gender' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date|before:today',
            'image' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required',
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered',
            'email.unique' => 'This email is already registered',
            'email.email' => 'Please provide a valid email address',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }
}
