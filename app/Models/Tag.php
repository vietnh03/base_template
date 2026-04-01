<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends BaseModel
{
    use HasUuids;
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
