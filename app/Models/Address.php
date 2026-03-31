<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends BaseModel
{
    protected $table = 'addresses';

    protected $fillable = [
        'address_type',
        'customer_id',
        'cart_id',
        'order_id',
        'first_name',
        'last_name',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
