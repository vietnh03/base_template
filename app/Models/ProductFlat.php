<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFlat extends Model
{
    protected $table = 'product_flat';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'url_key',
        'new',
        'featured',
        'status',
        'thumbnail',
        'price',
        'cost_price',
        'weight',
        'product_id',
        'parent_id',
        'locale',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'new' => 'boolean',
        'featured' => 'boolean',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
    ];
}
