<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AttributeOption extends BaseModel
{
    use HasUuids;
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'admin_name',
        'swatch_value',
        'sort_order',
    ];
}
