<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTagTranslation extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'post_tag_id',
        'locale',
        'name',
        'slug',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(PostTag::class, 'post_tag_id');
    }
}
