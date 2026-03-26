<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'locale',
        'text_value',
        'boolean_value',
        'integer_value',
        'float_value',
        'datetime_value',
        'date_value',
        'json_value',
    ];

    protected $casts = [
        'boolean_value' => 'boolean',
        'json_value' => 'json',
        'datetime_value' => 'datetime',
        'date_value' => 'date',
    ];
}
