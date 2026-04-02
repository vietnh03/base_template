<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use App\AppMain\Core\BaseFormRequest;

class StoreTransactionRequest extends BaseFormRequest
{
    public $order_id;
    public $transaction_id;
    public $amount;
    public $payment_method;

    public function rules(): array
    {
        return [
            'order_id' => 'required|string|exists:orders,id',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
        ];
    }
}
