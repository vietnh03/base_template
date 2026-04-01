<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class OrderTransactionDTO extends BaseDTO
{
    public ?string $transaction_id = null;
    public ?string $status = null;
    public ?string $type = null;
    public ?float $amount = null;
    public ?string $payment_method = null;
    public ?array $data = null;
    public ?string $invoice_id = null;
    public ?string $order_id = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
