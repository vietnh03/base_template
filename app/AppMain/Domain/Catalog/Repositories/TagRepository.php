<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Tag;

class TagRepository extends BaseRepository
{
    public function getModel()
    {
        return Tag::class;
    }

    public function findById($id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }

    public function create(array $data, array $translations = [])
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $translations) {
            $tag = $this->model->create($data);

            $this->saveTranslations($tag, $translations);

            return $tag->load('translations');
        });
    }

    public function update($id, array $data, array $translations = []): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data, $translations) {
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
        foreach ($translations as $locale => $translationData) {
            $tag->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'slug' => \Illuminate\Support\Str::slug($translationData['slug'] ?? $translationData['name']),
                ])
            );
        }
    }

    public function getTagsWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with('translations');

        if (!empty($filters['name'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (!empty($filters['slug'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('slug', 'like', '%' . $filters['slug'] . '%');
            });
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }
}