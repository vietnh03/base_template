<?php

namespace App\AppMain\Domain\Catalog\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\LockTimeoutException;

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
    public function generateUniqueUrlKey(string $name, string $locale, ?string $categoryId = null): string
    {
        $urlKey = Str::slug($name);
        $originalUrlKey = $urlKey;

        $lock = Cache::lock("category_url_key_{$locale}_{$originalUrlKey}", 5);

        try {
            $lock->block(5);

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
        } catch (LockTimeoutException $e) {
            throw new \RuntimeException('System is currently busy generating URL keys. Please try again.', 0, $e);
        } finally {
            optional($lock)->release();
        }
    }
}
