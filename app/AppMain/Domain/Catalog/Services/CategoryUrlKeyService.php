<?php

namespace App\AppMain\Domain\Catalog\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryUrlKeyService
{
    /**
     * Generate a unique URL key for a category in a specific locale.
     *
     * @param string $name
     * @param string $locale
     * @param int|null $categoryId
     * @return string
     */
    public function generateUniqueUrlKey(string $name, string $locale, ?int $categoryId = null): string
    {
        $urlKey = Str::slug($name);
        $originalUrlKey = $urlKey;

        // Use a lock to prevent race conditions during unique check
        $lock = Cache::lock("category_url_key_{$locale}_{$originalUrlKey}", 5);
        $lock->block(5);

        try {
            $i = 1;
            while (true) {
                $query = DB::table('category_translations')
                    ->where('url_key', $urlKey)
                    ->where('locale', $locale);

                if ($categoryId) {
                    $query->where('category_id', '!=', $categoryId);
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
