<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends BaseModel
{
    use HasUuids;
    protected $table = 'addresses';

    protected $fillable = [
        'address_type',
        'user_id',
        'cart_id',
        'order_id',
        'name',
        'gender',
        'company_name',
        'address',
        'address1',
        'city',
        'state',
        'country',
        'postcode',
        'email',
        'phone',
        'vat_id',
        'default_address',
        'additional',
    ];

    protected $casts = [
        'default_address' => 'boolean',
        'additional' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
