<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use App\AppMain\Core\BaseFormRequest;

class StoreInvoiceRequest extends BaseFormRequest
{
    public $order_id;
    public $grand_total;

    public function rules(): array
    {
        return [
            'order_id' => 'required|string|exists:orders,id',
            'grand_total' => 'nullable|numeric',
        ];
    }
}
