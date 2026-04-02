<?php

namespace App\AppMain\Domain\Post\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\LockTimeoutException;

class PostSlugService
{
    public function generateUniqueSlug(string $name, string $locale, string $table, $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;

        $lock = Cache::lock("post_slug_{$table}_{$locale}_{$originalSlug}", 5);

        try {
            $lock->block(5);

            $i = 1;
            while (true) {
                $query = DB::table($table)
                    ->where('slug', $slug)
                    ->where('locale', $locale);

                if ($ignoreId) {
                    if ($table === 'post_category_translations') {
                        $foreignKey = 'post_category_id';
                    } elseif ($table === 'post_tag_translations') {
                        $foreignKey = 'post_tag_id';
                    } else {
                        $foreignKey = 'post_id';
                    }

                    $query->where($foreignKey, '!=', $ignoreId);
                }

                if (!$query->exists()) {
                    break;
                }

                $slug = $originalSlug . '-' . $i++;
            }

            return $slug;
        } catch (LockTimeoutException $e) {
            throw new \RuntimeException('System is currently busy generating slugs. Please try again.', 0, $e);
        } finally {
            optional($lock)->release();
        }
    }
}
