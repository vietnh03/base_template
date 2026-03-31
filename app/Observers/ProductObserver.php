<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductFlat;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Handle the Product "saved" event.
     */
    public function saved(Product $product): void
    {
        $this->syncToFlat($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->syncToFlat($product);
    }

    /**
     * Sync basic product data to the flat table for all locales.
     */
    protected function syncToFlat(Product $product): void
    {
        $locales = $product->flat()->pluck('locale')->unique();

        // If no flat records exist yet, we might need a default locale or wait for attribute values
        if ($locales->isEmpty()) {
            return;
        }

        foreach ($locales as $locale) {
            ProductFlat::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $locale],
                [
                    'sku' => $product->sku,
                    'status' => $product->status,
                    'cost_price' => $product->cost_price,
                    'parent_id' => $product->parent_id,
                    'weight' => $product->weight,
                    'new' => $product->new,
                    'featured' => $product->featured,
                    'visible_individually' => $product->visible_individually,
                    'attribute_family_id' => $product->attribute_family_id,
                ]
            );
        }
    }
}
