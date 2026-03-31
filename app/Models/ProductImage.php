<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;

class ProductImage extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'path',
        'type',
        'position',
    ];

    protected static function booted()
    {
        static::deleting(function ($image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
        });
    }
}
