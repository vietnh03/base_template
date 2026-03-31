<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class TagResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $slug;
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = $model->id;
        $dto->name = $model->name;
        $dto->slug = $model->slug;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;
        return $dto;
    }
}
