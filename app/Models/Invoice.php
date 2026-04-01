<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends BaseModel
{
    use HasUuids;
    protected $fillable = [
        'increment_id',
        'state',
        'email_sent',
        'total_qty',
        'base_currency_code',
        'invoice_currency_code',
        'order_currency_code',
        'sub_total',
        'base_sub_total',
        'grand_total',
        'base_grand_total',
        'shipping_amount',
        'base_shipping_amount',
        'tax_amount',
        'base_tax_amount',
        'discount_amount',
        'base_discount_amount',
        'order_id',
        'order_address_id',
        'transaction_id',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
