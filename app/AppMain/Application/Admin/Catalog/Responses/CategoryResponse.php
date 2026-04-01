<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CategoryResponse extends BaseResponseDTO
{
    public $id;
    public $parentId;
    public $position;
    public $status;
    public $logoUrl;
    public $bannerUrl;
    public $additional;
    public $translations = [];
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->parentId = $model->parent_id;
        $dto->position = $model->position;
        $dto->status = $model->status;
        $dto->logoUrl = $model->logo_path ? \Illuminate\Support\Facades\Storage::url($model->logo_path) : null;
        $dto->bannerUrl = $model->banner_path ? \Illuminate\Support\Facades\Storage::url($model->banner_path) : null;
        $dto->additional = $model->additional;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        if ($model->relationLoaded('translations')) {
            foreach ($model->translations as $translation) {
                $dto->translations[$translation->locale] = [
                    'name' => $translation->name,
                    'slug' => $translation->slug,
                    'urlKey' => $translation->url_key,
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
