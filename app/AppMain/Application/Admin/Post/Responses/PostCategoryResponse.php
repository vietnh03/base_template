<?php

namespace App\AppMain\Application\Admin\Post\Responses;

use App\AppMain\Core\BaseResponseDTO;
use Illuminate\Support\Facades\Storage;

class PostCategoryResponse extends BaseResponseDTO
{
    public $id;
    public $parentId;
    public $status;
    public $position;
    public $imageUrl;
    public $translations = [];
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->parentId = $model->parent_id;
        $dto->status = $model->status;
        $dto->position = $model->position;
        $dto->imageUrl = $model->image_path ? Storage::url($model->image_path) : null;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

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
