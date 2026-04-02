<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CmsPageTranslation extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'cms_page_id',
        'locale',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
}
