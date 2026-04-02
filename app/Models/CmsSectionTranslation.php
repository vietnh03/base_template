<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CmsSectionTranslation extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'cms_section_id',
        'locale',
        'content',
    ];

    protected $casts = [
        'content' => 'json',
    ];
}
