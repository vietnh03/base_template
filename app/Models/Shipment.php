<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends BaseModel
{
    use HasUuids;
    protected $fillable = [
        'status',
        'total_qty',
        'total_weight',
        'carrier_code',
        'carrier_title',
        'track_number',
        'email_sent',
        'user_id',
        'user_type',
        'order_id',
        'order_address_id',
        'inventory_source_id',
        'inventory_source_name',
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
