<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class TagResponse extends BaseResponseDTO
{
    public $id;
    public $translations = [];
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        if ($model->relationLoaded('translations')) {
            foreach ($model->translations as $translation) {
                $dto->translations[$translation->locale] = [
                    'name' => $translation->name,
                    'slug' => $translation->slug,
                ];
            }
        }
        return $dto;
    }
}
