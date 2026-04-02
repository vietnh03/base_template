<?php

namespace App\AppMain\Application\Api\Post\Responses;

use App\AppMain\Core\BaseResponseDTO;
use Illuminate\Support\Facades\Storage;

class PostResponse extends BaseResponseDTO
{
    public $id;
    public $imageUrl;
    public $publishedAt;
    public $translations = [];
    public $categories = [];
    public $tags = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->imageUrl = $model->image_path ? Storage::url($model->image_path) : null;
        $dto->publishedAt = $model->published_at;

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
                    'slug' => $category->translations->where('locale', app()->getLocale())->first()->slug ?? ($category->translations->first()->slug ?? null),
                ];
            }
        }

        if ($model->relationLoaded('tags')) {
            foreach ($model->tags as $tag) {
                $dto->tags[] = [
                    'id' => $tag->id,
                    'name' => $tag->translations->where('locale', app()->getLocale())->first()->name ?? ($tag->translations->first()->name ?? null),
                    'slug' => $tag->translations->where('locale', app()->getLocale())->first()->slug ?? ($tag->translations->first()->slug ?? null),
                ];
            }
        }

        return $dto;
    }
}
