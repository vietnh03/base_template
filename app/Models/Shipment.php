<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends BaseModel
{
    protected $fillable = [
        'status',
        'total_qty',
        'total_weight',
        'carrier_title',
        'track_number',
        'email_sent',
        'customer_id',
        'customer_type',
        'order_id',
        'order_address_id',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // items...
}
