<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends BaseModel
{
    use HasUuids;
    protected $fillable = [
        'quantity',
        'sku',
        'name',
        'coupon_code',
        'weight',
        'total_weight',
        'base_total_weight',
        'price',
        'base_price',
        'custom_price',
        'total',
        'base_total',
        'tax_percent',
        'tax_amount',
        'base_tax_amount',
        'discount_percent',
        'discount_amount',
        'base_discount_amount',
        'parent_id',
        'product_id',
        'cart_id',
        'tax_category_id',
        'applied_cart_rule_ids',
        'additional',
    ];

    protected $casts = [
        'additional' => 'json',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
