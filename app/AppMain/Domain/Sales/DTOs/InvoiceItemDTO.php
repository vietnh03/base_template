<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class InvoiceItemDTO extends BaseDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $sku = null;
    public ?int $qty = null;
    public ?float $price = null;
    public ?float $base_price = null;
    public ?float $total = null;
    public ?float $base_total = null;
    public ?float $tax_amount = null;
    public ?float $base_tax_amount = null;
    public ?string $product_id = null;
    public ?string $product_type = null;
    public ?string $order_item_id = null;
    public ?string $invoice_id = null;
    public ?string $parent_id = null;
    public ?array $additional = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
