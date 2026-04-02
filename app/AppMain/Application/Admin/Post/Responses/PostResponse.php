<?php

namespace App\AppMain\Application\Admin\Post\Responses;

use App\AppMain\Core\BaseResponseDTO;
use Illuminate\Support\Facades\Storage;

class PostResponse extends BaseResponseDTO
{
    public $id;
    public $status;
    public $imageUrl;
    public $authorId;
    public $publishedAt;
    public $translations = [];
    public $categories = [];
    public $tags = [];
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->status = $model->status;
        $dto->imageUrl = $model->image_path ? Storage::url($model->image_path) : null;
        $dto->authorId = $model->author_id;
        $dto->publishedAt = $model->published_at;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        if ($model->relationLoaded('translations')) {
            foreach ($model->translations as $translation) {
                $dto->translations[$translation->locale] = [
                    'name' => $translation->name,
                    'slug' => $translation->slug,
                    'shortDescription' => $translation->short_description,
                    'content' => $translation->content,
                    'metaTitle' => $translation->meta_title,
                    'metaKeywords' => $translation->meta_keywords,
                    'metaDescription' => $translation->meta_description,
                ];
            }
        }

        if ($model->relationLoaded('categories')) {
            foreach ($model->categories as $category) {
                $dto->categories[] = [
                    'id' => $category->id,
                    'name' => $category->translations->where('locale', app()->getLocale())->first()->name ?? ($category->translations->first()->name ?? null),
                ];
            }
        }

        if ($model->relationLoaded('tags')) {
            foreach ($model->tags as $tag) {
                $dto->tags[] = [
                    'id' => $tag->id,
                    'name' => $tag->translations->where('locale', app()->getLocale())->first()->name ?? ($tag->translations->first()->name ?? null),
                ];
            }
        }

        return $dto;
    }
}
