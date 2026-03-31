<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\AppMain\Domain\Catalog\Services\CategoryUrlKeyService;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

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

            $category->update($data);

            if (!empty($translations)) {
                $this->saveTranslations($category, $translations);
            }

            return true;
        });
    }

    protected function saveTranslations($category, array $translations)
    {
        $urlKeyService = app(CategoryUrlKeyService::class);

        foreach ($translations as $locale => $translationData) {
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'url_key' => $urlKeyService->generateUniqueUrlKey(
                        $translationData['url_key'] ?? $translationData['name'],
                        $locale,
                        $category->id
                    ),
                ])
            );
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

    public function delete($id): bool
    {
        $category = $this->findById($id);

        return DB::transaction(function () use ($category) {
            // Delete translations
            $category->translations()->delete();

            // Detach from products in pivot table
            if (method_exists($category, 'products')) {
                $category->products()->detach();
            }

            return $category->delete();
        });
    }
}
