<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends BaseModel
{
    use HasUuids;
    protected $table = 'cart';

    protected $fillable = [
        'customer_email',
        'customer_first_name',
        'customer_last_name',
        'shipping_method',
        'coupon_code',
        'is_gift',
        'items_count',
        'items_qty',
        'exchange_rate',
        'global_currency_code',
        'base_currency_code',
        'cart_currency_code',
        'grand_total',
        'base_grand_total',
        'sub_total',
        'base_sub_total',
        'tax_total',
        'base_tax_total',
        'discount_amount',
        'base_discount_amount',
        'checkout_method',
        'is_guest',
        'is_active',
        'applied_cart_rule_ids',
        'customer_id',
    ];

    protected $casts = [
        'is_gift' => 'boolean',
        'is_guest' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->whereIn('address_type', ['cart_billing', 'cart_shipping']);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
