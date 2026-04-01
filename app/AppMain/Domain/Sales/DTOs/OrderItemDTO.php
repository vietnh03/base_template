<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class OrderItemDTO extends BaseDTO
{
    public ?string $sku = null;
    public ?string $name = null;
    public ?string $coupon_code = null;
    public ?float $weight = 0;
    public ?int $qty_ordered = 0;
    public ?float $price = 0;
    public ?float $base_price = 0;
    public ?float $total = 0;
    public ?float $base_total = 0;
    public ?int $product_id = null;
    public ?int $order_id = null;
    public ?int $parent_id = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
