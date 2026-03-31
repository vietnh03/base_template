<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    protected $fillable = [
        'increment_id',
        'status',
        'is_guest',
        'customer_email',
        'customer_first_name',
        'customer_last_name',
        'shipping_method',
        'shipping_title',
        'shipping_description',
        'coupon_code',
        'is_gift',
        'total_item_count',
        'total_qty_ordered',
        'base_currency_code',
        'order_currency_code',
        'grand_total',
        'base_grand_total',
        'grand_total_invoiced',
        'base_grand_total_invoiced',
        'grand_total_refunded',
        'base_grand_total_refunded',
        'sub_total',
        'base_sub_total',
        'sub_total_invoiced',
        'base_sub_total_invoiced',
        'sub_total_refunded',
        'base_sub_total_refunded',
        'discount_percent',
        'discount_amount',
        'base_discount_amount',
        'discount_invoiced',
        'base_discount_invoiced',
        'discount_refunded',
        'base_discount_refunded',
        'tax_amount',
        'base_tax_amount',
        'tax_amount_invoiced',
        'base_tax_amount_invoiced',
        'tax_amount_refunded',
        'base_tax_amount_refunded',
        'shipping_amount',
        'base_shipping_amount',
        'shipping_invoiced',
        'base_shipping_invoiced',
        'shipping_refunded',
        'base_shipping_refunded',
        'shipping_discount_amount',
        'base_shipping_discount_amount',
        'customer_id',
        'customer_type',
        'cart_id',
        'applied_cart_rule_ids',
    ];

    protected $casts = [
        'is_guest' => 'boolean',
        'is_gift' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->whereIn('address_type', ['order_billing', 'order_shipping']);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
