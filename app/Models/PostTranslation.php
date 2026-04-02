<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'post_id',
        'locale',
        'name',
        'slug',
        'short_description',
        'content',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
