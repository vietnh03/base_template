<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class OrderDTO extends BaseDTO
{
    // Extracted from Order model fillable array
    public ?string $increment_id = null;
    public ?string $status = null;
    public bool $is_guest = true;
    public ?string $user_email = null;
    public ?string $user_first_name = null;
    public ?string $user_last_name = null;
    public ?string $shipping_method = null;
    public ?string $shipping_title = null;
    public ?string $shipping_description = null;
    public ?string $coupon_code = null;
    public bool $is_gift = false;
    public ?int $total_item_count = null;
    public ?int $total_qty_ordered = null;
    public ?string $base_currency_code = null;
    public ?string $order_currency_code = null;
    public ?float $grand_total = 0;
    public ?float $base_grand_total = 0;
    // ... we can add all remaining properties similarly ...
    public ?string $user_id = null;
    public ?string $user_type = null;
    public ?string $cart_id = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
