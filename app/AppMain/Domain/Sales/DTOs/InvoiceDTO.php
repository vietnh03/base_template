<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class InvoiceDTO extends BaseDTO
{
    public ?string $increment_id = null;
    public ?string $state = null;
    public ?bool $email_sent = null;
    public ?int $total_qty = null;
    public ?string $base_currency_code = null;
    public ?string $invoice_currency_code = null;
    public ?string $order_currency_code = null;
    public ?float $sub_total = null;
    public ?float $base_sub_total = null;
    public ?float $grand_total = null;
    public ?float $base_grand_total = null;
    public ?float $shipping_amount = null;
    public ?float $base_shipping_amount = null;
    public ?float $tax_amount = null;
    public ?float $base_tax_amount = null;
    public ?float $discount_amount = null;
    public ?float $base_discount_amount = null;
    public ?string $order_id = null;
    public ?string $order_address_id = null;
    public ?string $transaction_id = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
