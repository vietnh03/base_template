<?php

namespace App\AppMain\Domain\Post\Repositories;

use App\AppMain\Core\BaseRepository;
use App\AppMain\Domain\Post\Services\PostSlugService;
use App\Models\PostCategory;
use Illuminate\Support\Facades\DB;
use App\AppMain\Core\Helpers\FileUploadService;

class PostCategoryRepository extends BaseRepository
{
    protected FileUploadService $fileUploadService;

    public function __construct(PostCategory $model, FileUploadService $fileUploadService)
    {
        $this->model = $model;
        $this->fileUploadService = $fileUploadService;
    }
    public function getModel()
    {
        return PostCategory::class;
    }

    public function findById($id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }

    public function create(array $data, array $translations = [])
    {
        return DB::transaction(function () use ($data, $translations) {
            $data = $this->handleImage($data);
            $category = $this->model->create($data);

            $this->saveTranslations($category, $translations);

            return $category->load('translations');
        });
    }

    public function update($id, array $data, array $translations = []): bool
    {
        return DB::transaction(function () use ($id, $data, $translations) {
            $category = $this->findById($id);

            $data = $this->handleImage($data, $category);
            $category->update($data);

            if (!empty($translations)) {
                $this->saveTranslations($category, $translations);
            }

            return true;
        });
    }

    protected function saveTranslations($category, array $translations)
    {
        $slugService = app(PostSlugService::class);

        foreach ($translations as $key => $translationData) {
            $locale = $translationData['locale'] ?? $key;

            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'slug' => $slugService->generateUniqueSlug(
                        $translationData['slug'] ?? $translationData['name'],
                        $locale,
                        'post_category_translations',
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

    protected function handleImage(array $data, $category = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($category && $category->image_path) {
                $this->fileUploadService->delete($category->image_path);
            }
            $data['image_path'] = $this->fileUploadService->upload($data['image'], 'posts/categories');
        }

        unset($data['image']);

        return $data;
    }

    public function delete($id): bool
    {
        $category = $this->findById($id);

        return DB::transaction(function () use ($category) {
            $category->translations()->delete();
            return $category->delete();
        });
    }
}
