<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;

class TagTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'tag_id',
        'locale',
        'name',
        'slug',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
