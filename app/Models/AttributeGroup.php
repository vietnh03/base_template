<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeGroup extends Model
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
}
