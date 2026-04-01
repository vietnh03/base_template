<?php

namespace App\AppMain\Application\User\Checkout\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|string|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ];
    }
}
