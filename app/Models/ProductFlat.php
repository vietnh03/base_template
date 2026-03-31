<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;

class ProductFlat extends BaseModel
{
    protected $table = 'product_flat';

    protected $fillable = [
        'sku',
        'type',
        'product_number',
        'name',
        'short_description',
        'description',
        'url_key',
        'new',
        'featured',
        'status',
        'thumbnail',
        'price',
        'cost_price',
        'special_price',
        'special_price_from',
        'special_price_to',
        'weight',
        'product_id',
        'parent_id',
        'attribute_family_id',
        'locale',
        'visible_individually',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'new' => 'boolean',
        'featured' => 'boolean',
        'status' => 'boolean',
        'visible_individually' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'special_price_from' => 'date',
        'special_price_to' => 'date',
        'weight' => 'decimal:2',
    ];
}
