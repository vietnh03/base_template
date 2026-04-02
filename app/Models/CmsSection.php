<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsSection extends BaseModel
{
    use HasUuids;

    protected $fillable = [
        'page_id',
        'type',
        'requires_data_source',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'requires_data_source' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CmsSectionTranslation::class, 'cms_section_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }
}
