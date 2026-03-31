<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends BaseModel
{
    protected $fillable = [
        'status',
    ];

    public function translations()
    {
        return $this->hasMany(TagTranslation::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
