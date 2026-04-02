<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CmsPageTranslation::class, 'cms_page_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CmsSection::class, 'page_id')->orderBy('sort_order');
    }
}
