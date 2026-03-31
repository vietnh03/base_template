<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;

class AttributeOption extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'admin_name',
        'swatch_value',
        'sort_order',
    ];
}
