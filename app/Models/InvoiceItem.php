<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'qty',
        'price',
        'base_price',
        'total',
        'base_total',
        'tax_amount',
        'base_tax_amount',
        'product_id',
        'product_type',
        'order_item_id',
        'invoice_id',
        'parent_id',
        'additional',
    ];

    protected $casts = [
        'additional' => 'json',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
