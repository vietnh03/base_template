<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostCategoryTranslation extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'post_category_id',
        'locale',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }
}
