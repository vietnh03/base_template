<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends BaseModel
{
    protected $fillable = [
        'attribute_family_id',
        'name',
        'position',
    ];

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_group_mappings');
    }

    public function attribute_group_mappings(): HasMany
    {
        return $this->hasMany(AttributeGroupMapping::class);
    }
}
