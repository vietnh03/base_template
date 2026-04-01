<?php

namespace App\AppMain\Domain\Catalog\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\LockTimeoutException;

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

        try {
            $lock->block(5);

            $i = 1;
            // Optimize by finding the highest existing suffix in one go
            $existingKeys = DB::table('product_flat')
                ->where('locale', $locale)
                ->where('url_key', 'LIKE', $originalUrlKey . '%')
                ->pluck('url_key')
                ->toArray();

            if (!in_array($originalUrlKey, $existingKeys)) {
                return $originalUrlKey;
            }

            // Find the highest N in url-key-N
            $maxSuffix = 0;
            foreach ($existingKeys as $key) {
                if (preg_match('/' . preg_quote($originalUrlKey, '/') . '-(\d+)$/', $key, $matches)) {
                    $maxSuffix = max($maxSuffix, (int) $matches[1]);
                }
            }

            return $originalUrlKey . '-' . ($maxSuffix + 1);
        } catch (LockTimeoutException $e) {
            throw new \RuntimeException('System is currently busy generating URL keys. Please try again.', 0, $e);
        } finally {
            optional($lock)->release();
        }
    }
}
