<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'admin_name',
        'sort_order',
    ];
}
