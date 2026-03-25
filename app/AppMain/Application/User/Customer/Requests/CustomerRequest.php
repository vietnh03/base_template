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
            'full_name' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:100',
            'phone_number' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:20',
                Rule::unique('customers', 'phone_number')->ignore($customerId, 'id')
            ],
            'customer_type' => ($isUpdate ? 'sometimes|' : '') . 'required|in:Individual,Business',

            // Optional fields
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('customers', 'email')->ignore($customerId, 'id')
            ],
            'customer_status' => 'nullable|in:Lead,Active,Inactive,VIP',
            'assigned_staff_id' => 'nullable|uuid',
            'address' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'source' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Customer name is required',
            'phone_number.required' => 'Phone number is required',
            'phone_number.unique' => 'This phone number is already registered',
            'email.unique' => 'This email is already registered',
            'email.email' => 'Please provide a valid email address',
            'customer_type.required' => 'Customer type is required',
            'customer_type.in' => 'Customer type must be Individual or Business',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }
}
