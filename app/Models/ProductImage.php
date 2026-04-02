<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductImage extends BaseModel
{
    use HasUuids;
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
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($image->path);
        });
    }
}
