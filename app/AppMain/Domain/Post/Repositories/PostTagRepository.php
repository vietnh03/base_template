<?php

namespace App\AppMain\Domain\Post\Repositories;

use App\AppMain\Core\BaseRepository;
use App\AppMain\Domain\Post\Services\PostSlugService;
use App\Models\PostTag;
use Illuminate\Support\Facades\DB;

class PostTagRepository extends BaseRepository
{
    public function getModel()
    {
        return PostTag::class;
    }

    public function findById($id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }

    public function create(array $data, array $translations = [])
    {
        return DB::transaction(function () use ($data, $translations) {
            $tag = $this->model->create($data);

            $this->saveTranslations($tag, $translations);

            return $tag->load('translations');
        });
    }

    public function update($id, array $data, array $translations = []): bool
    {
        return DB::transaction(function () use ($id, $data, $translations) {
            $tag = $this->findById($id);
            $tag->update($data);

            if (!empty($translations)) {
                $this->saveTranslations($tag, $translations);
            }

            return true;
        });
    }

    protected function saveTranslations($tag, array $translations)
    {
        $slugService = app(PostSlugService::class);

        foreach ($translations as $key => $translationData) {
            $locale = $translationData['locale'] ?? $key;

            $tag->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'slug' => $slugService->generateUniqueSlug(
                        $translationData['slug'] ?? $translationData['name'],
                        $locale,
                        'post_tag_translations',
                        $tag->id
                    ),
                ])
            );
        }
    }

    public function getTagsWithFilters($filters = [])
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
        $tag = $this->findById($id);

        return DB::transaction(function () use ($tag) {
            $tag->translations()->delete();
            return $tag->delete();
        });
    }
}
