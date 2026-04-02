<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductAttributeValue extends BaseModel
{
    use HasUuids;
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