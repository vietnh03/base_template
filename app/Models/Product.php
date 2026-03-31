<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends BaseModel
{
    protected $fillable = [
        'sku',
        'status',
        'parent_id',
        'attribute_family_id',
        'additional',
    ];

    protected $casts = [
        'status' => 'boolean',
        'additional' => 'json',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attribute_values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function flat(): HasMany
    {
        return $this->hasMany(ProductFlat::class);
    }

    public function attribute_family(): BelongsTo
    {
        return $this->belongsTo(AttributeFamily::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function up_sells(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_up_sells', 'parent_id', 'child_id');
    }

    public function cross_sells(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_cross_sells', 'parent_id', 'child_id');
    }

    public function super_attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_super_attributes');
    }
}
