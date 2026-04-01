<?php

namespace App\AppMain\Domain\Catalog\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductUrlKeyService
{
    /**
     * Generate a unique URL key for a product in a specific locale.
     *
     * @param string $name
     * @param string $locale
     * @param int|null $productId
     * @return string
     */
    public function generateUniqueUrlKey(string $name, string $locale, ?string $productId = null): string
    {
        $urlKey = Str::slug($name);
        $originalUrlKey = $urlKey;

        // Use a lock to prevent race conditions during unique check
        $lock = Cache::lock("product_url_key_{$locale}_{$originalUrlKey}", 5);
        $lock->block(5);

        try {
            $i = 1;
            while (true) {
                $query = DB::table('product_flat')
                    ->where('url_key', $urlKey)
                    ->where('locale', $locale);

                if ($productId) {
                    $query->where('product_id', '!=', $productId);
                }

                if (!$query->exists()) {
                    break;
                }

                $urlKey = $originalUrlKey . '-' . $i++;
            }

            return $urlKey;
        } finally {
            $lock->release();
        }
    }
}
