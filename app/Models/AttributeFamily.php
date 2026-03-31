<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeFamily extends BaseModel
{
    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(AttributeGroup::class);
    }
}