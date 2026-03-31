<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;

class ProductInventory extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'qty',
    ];
}
