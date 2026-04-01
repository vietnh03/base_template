<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'customer_email' => 'required|email|max:255',
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',

            'shipping_method' => 'nullable|string|max:255',
            'coupon_code' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',

            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string|max:255',
            'shipping_address.last_name' => 'required|string|max:255',
            'shipping_address.address' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.country' => 'required|string|max:2',
            'shipping_address.postcode' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:255',

            'billing_address' => 'required|array',
            'billing_address.first_name' => 'required|string|max:255',
            'billing_address.last_name' => 'required|string|max:255',
            'billing_address.address' => 'required|string|max:255',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.country' => 'required|string|max:2',
            'billing_address.postcode' => 'required|string|max:255',
            'billing_address.phone' => 'required|string|max:255',
        ];
    }
}
