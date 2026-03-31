<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends BaseModel
{
    protected $fillable = [
        'code',
        'admin_name',
        'type',
        'is_required',
        'is_unique',
        'is_filterable',
        'is_configurable',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_filterable' => 'boolean',
        'is_configurable' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }
}