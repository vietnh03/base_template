<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CategoryTranslation extends BaseModel
{
    use HasUuids;
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
        'description',
        'url_key',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
}