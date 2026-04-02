<?php

namespace App\AppMain\Domain\Checkout\DTOs;

use App\AppMain\Core\BaseDTO;

class CartDTO extends BaseDTO
{
    public ?string $user_email = null;
    public ?string $user_first_name = null;
    public ?string $user_last_name = null;
    public ?string $shipping_method = null;
    public ?string $coupon_code = null;
    public bool $is_gift = false;
    public ?int $items_count = 0;
    public ?float $items_qty = 0;
    public ?float $exchange_rate = 1;
    public ?string $global_currency_code = null;
    public ?string $base_currency_code = null;
    public ?string $cart_currency_code = null;
    public ?float $grand_total = 0;
    public ?float $base_grand_total = 0;
    public ?float $sub_total = 0;
    public ?float $base_sub_total = 0;
    public ?float $tax_total = 0;
    public ?float $base_tax_total = 0;
    public ?float $discount_amount = 0;
    public ?float $base_discount_amount = 0;
    public ?string $checkout_method = null;
    public bool $is_guest = true;
    public bool $is_active = true;
    public ?string $applied_cart_rule_ids = null;
    public ?string $user_id = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
