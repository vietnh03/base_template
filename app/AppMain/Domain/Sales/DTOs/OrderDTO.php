<?php

namespace App\AppMain\Domain\Sales\DTOs;

use App\AppMain\Core\BaseDTO;

class OrderDTO extends BaseDTO
{
    // Extracted from Order model fillable array
    public ?string $increment_id = null;
    public ?string $status = null;
    public ?string $payment_status = 'pending';
    public bool $is_guest = true;
    public ?string $user_email = null;
    public ?string $user_name = null;
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
    public ?float $grand_total_invoiced = 0;
    public ?float $base_grand_total_invoiced = 0;
    public ?float $grand_total_refunded = 0;
    public ?float $base_grand_total_refunded = 0;
    public ?float $sub_total = 0;
    public ?float $base_sub_total = 0;
    public ?float $sub_total_invoiced = 0;
    public ?float $base_sub_total_invoiced = 0;
    public ?float $sub_total_refunded = 0;
    public ?float $base_sub_total_refunded = 0;
    public ?float $discount_percent = 0;
    public ?float $discount_amount = 0;
    public ?float $base_discount_amount = 0;
    public ?float $discount_invoiced = 0;
    public ?float $base_discount_invoiced = 0;
    public ?float $discount_refunded = 0;
    public ?float $base_discount_refunded = 0;
    public ?float $tax_amount = 0;
    public ?float $base_tax_amount = 0;
    public ?float $tax_amount_invoiced = 0;
    public ?float $base_tax_amount_invoiced = 0;
    public ?float $tax_amount_refunded = 0;
    public ?float $base_tax_amount_refunded = 0;
    public ?float $shipping_amount = 0;
    public ?float $base_shipping_amount = 0;
    public ?float $shipping_invoiced = 0;
    public ?float $base_shipping_invoiced = 0;
    public ?float $shipping_refunded = 0;
    public ?float $base_shipping_refunded = 0;
    public ?float $shipping_discount_amount = 0;
    public ?float $base_shipping_discount_amount = 0;
    public ?string $user_id = null;
    public ?string $user_type = null;
    public ?string $cart_id = null;
    public ?string $applied_cart_rule_ids = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
