<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductInventory extends BaseModel
{
    use HasUuids;
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'qty',
    ];
}
