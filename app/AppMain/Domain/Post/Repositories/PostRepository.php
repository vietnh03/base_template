<?php

namespace App\AppMain\Domain\Post\Repositories;

use App\AppMain\Core\BaseRepository;
use App\AppMain\Domain\Post\Services\PostSlugService;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostRepository extends BaseRepository
{
    protected $fileUploadService;

    public function __construct(Post $model, \App\AppMain\Core\Helpers\FileUploadService $fileUploadService)
    {
        $this->model = $model;
        $this->fileUploadService = $fileUploadService;
    }

    public function getModel()
    {
        return Post::class;
    }

    public function findById($id)
    {
        return $this->model->with(['translations', 'categories.translations', 'tags.translations'])->findOrFail($id);
    }

    public function create(array $data, array $translations = [], array $categories = [], array $tags = [])
    {
        return DB::transaction(function () use ($data, $translations, $categories, $tags) {
            $data = $this->handleImage($data);
            $post = $this->model->create($data);

            $this->saveTranslations($post, $translations);

            if (!empty($categories)) {
                $post->categories()->sync($categories);
            }

            if (!empty($tags)) {
                $post->tags()->sync($tags);
            }

            return $post->load(['translations', 'categories.translations', 'tags.translations']);
        });
    }

    public function update($id, array $data, array $translations = [], array $categories = null, array $tags = null): bool
    {
        return DB::transaction(function () use ($id, $data, $translations, $categories, $tags) {
            $post = $this->findById($id);

            $data = $this->handleImage($data, $post);
            $post->update($data);

            if (!empty($translations)) {
                $this->saveTranslations($post, $translations);
            }

            if ($categories !== null) {
                $post->categories()->sync($categories);
            }

            if ($tags !== null) {
                $post->tags()->sync($tags);
            }

            return true;
        });
    }

    protected function saveTranslations($post, array $translations)
    {
        $slugService = app(PostSlugService::class);

        foreach ($translations as $locale => $translationData) {
            $post->translations()->updateOrCreate(
                ['locale' => $locale],
                array_merge($translationData, [
                    'slug' => $slugService->generateUniqueSlug(
                        $translationData['slug'] ?? $translationData['name'],
                        $locale,
                        'post_translations',
                        $post->id
                    ),
                ])
            );
        }
    }

    public function getPostsWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with(['translations', 'categories.translations', 'tags.translations']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('post_categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('post_tags.id', $filters['tag_id']);
            });
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }

    protected function handleImage(array $data, $post = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($post && $post->image_path) {
                $this->fileUploadService->delete($post->image_path);
            }
            $data['image_path'] = $this->fileUploadService->upload($data['image'], 'posts/images');
        }

        unset($data['image']);

        return $data;
    }

    public function delete($id): bool
    {
        $post = $this->findById($id);

        return DB::transaction(function () use ($post) {
            if ($post->image_path) {
                $this->fileUploadService->delete($post->image_path);
            }
            $post->translations()->delete();
            $post->categories()->detach();
            $post->tags()->detach();
            return $post->delete();
        });
    }
}
