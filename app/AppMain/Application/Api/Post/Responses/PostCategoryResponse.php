<?php

namespace App\AppMain\Application\Api\Post\Responses;

use App\AppMain\Core\BaseResponseDTO;
use Illuminate\Support\Facades\Storage;

class PostCategoryResponse extends BaseResponseDTO
{
    public $id;
    public $parentId;
    public $position;
    public $imageUrl;
    public $translations = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->parentId = $model->parent_id;
        $dto->position = $model->position;
        $dto->imageUrl = $model->image_path ? Storage::url($model->image_path) : null;

        if ($model->relationLoaded('translations')) {
            foreach ($model->translations as $translation) {
                $dto->translations[$translation->locale] = [
                    'name' => $translation->name,
                    'slug' => $translation->slug,
                    'description' => $translation->description,
                    'metaTitle' => $translation->meta_title,
                    'metaKeywords' => $translation->meta_keywords,
                    'metaDescription' => $translation->meta_description,
                ];
            }
        }

        return $dto;
    }
}
