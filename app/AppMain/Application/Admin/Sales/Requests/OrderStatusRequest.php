<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,processing,completed,canceled,closed,fraud',
            'comment' => 'nullable|string|max:1000'
        ];
    }
}
