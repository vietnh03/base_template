<?php

use App\AppMain\Core\BaseRepository;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return Category::class;
    }

    public function findById($id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }

    public function create(array $data, array $translations = [])
    {
        return DB::transaction(function () use ($data, $translations) {
            $category = $this->model->create($data);

            $this->saveTranslations($category, $translations);

            return $category->load('translations');
        });
    }

    public function update($id, array $data, array $translations = []): bool
    {
        return DB::transaction(function () use ($id, $data, $translations) {
            $category = $this->findById($id);
            if (!$category) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            $category->update($data);

            if (!empty($translations)) {
                $this->saveTranslations($category, $translations);
            }

            return true;
        });
    }

    protected function saveTranslations($category, array $translations)
    {
        foreach ($translations as $locale => $translationData) {
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'url_key' => $this->generateUniqueUrlKey($translationData['url_key'] ?? $translationData['name'], $locale, $category->id),
                ])
            );
        }
    }

    protected function generateUniqueUrlKey($name, $locale, $categoryId = null): string
    {
        $urlKey = Str::slug($name);
        $originalUrlKey = $urlKey;

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

    public function getCategoriesWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with('translations');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }
}
