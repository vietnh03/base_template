<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TagTranslation extends BaseModel
{
    use HasUuids;
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
