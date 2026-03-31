<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductFlat;

class ProductObserver
{
    /**
     * Handle the Product "saved" event.
     * Fires for both create and update — no need for a separate updated() hook.
     */
    public function saved(Product $product): void
    {
        $this->syncToFlat($product);
    }

    /**
     * Sync basic product scalar fields to the flat table for all existing locales.
     * EAV attribute values are synced separately by ProductRepository::syncToFlat().
     */
    protected function syncToFlat(Product $product): void
    {
        $locales = $product->flat()->pluck('locale')->unique();

        if ($locales->isEmpty()) {
            $locales = collect([config('app.locale', 'vi')]);
        }

        foreach ($locales as $locale) {
            ProductFlat::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $locale],
                [
                    'sku' => $product->sku,
                    'status' => $product->status,
                    'parent_id' => $product->parent_id,
                    'attribute_family_id' => $product->attribute_family_id,
                ]
            );
        }
    }
}
