<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'parent_id',
        'status',
        'position',
        'image_path',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PostCategoryTranslation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category', 'post_category_id', 'post_id');
    }
}
