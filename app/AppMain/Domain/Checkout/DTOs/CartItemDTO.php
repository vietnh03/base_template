<?php

namespace App\AppMain\Domain\Checkout\DTOs;

use App\AppMain\Core\BaseDTO;

class CartItemDTO extends BaseDTO
{
    public int $quantity = 0;
    public ?string $sku = null;
    public ?string $name = null;
    public ?string $coupon_code = null;
    public float $weight = 0;
    public float $total_weight = 0;
    public float $base_total_weight = 0;
    public float $price = 0;
    public float $base_price = 0;
    public ?float $custom_price = null;
    public float $total = 0;
    public float $base_total = 0;
    public float $tax_percent = 0;
    public ?float $tax_amount = 0;
    public ?float $base_tax_amount = 0;
    public float $discount_percent = 0;
    public float $discount_amount = 0;
    public float $base_discount_amount = 0;
    public ?string $parent_id = null;
    public string $product_id;
    public string $cart_id;
    public ?string $tax_category_id = null;
    public ?string $applied_cart_rule_ids = null;
    public ?array $additional = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
