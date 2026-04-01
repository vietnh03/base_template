<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends BaseModel
{
    use HasUuids;
    protected $fillable = [
        'sku',
        'name',
        'coupon_code',
        'weight',
        'total_weight',
        'qty_ordered',
        'qty_shipped',
        'qty_invoiced',
        'qty_canceled',
        'qty_refunded',
        'price',
        'base_price',
        'total',
        'base_total',
        'total_invoiced',
        'base_total_invoiced',
        'amount_refunded',
        'base_amount_refunded',
        'discount_percent',
        'discount_amount',
        'base_discount_amount',
        'discount_invoiced',
        'base_discount_invoiced',
        'discount_refunded',
        'base_discount_refunded',
        'tax_percent',
        'tax_amount',
        'base_tax_amount',
        'tax_amount_invoiced',
        'base_tax_amount_invoiced',
        'tax_amount_refunded',
        'base_tax_amount_refunded',
        'product_id',
        'order_id',
        'parent_id',
        'additional',
    ];

    protected $casts = [
        'additional' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
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
