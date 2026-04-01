<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTransaction extends BaseModel
{
    use HasUuids;
    protected $fillable = [
        'transaction_id',
        'status',
        'type',
        'amount',
        'payment_method',
        'data',
        'invoice_id',
        'order_id',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
