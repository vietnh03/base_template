<?php

namespace App\AppMain\Application\Api\Cms\Responses;

use App\AppMain\Core\BaseResponseDTO;

class SeoResponse extends BaseResponseDTO
{
    public ?string $meta_title = null;
    public ?string $meta_keywords = null;
    public ?string $meta_description = null;

    public static function fromModel($model): self
    {
        $dto = new self();

        $translation = $model->translations->first();
        $dto->meta_title = $translation?->meta_title ?? null;
        $dto->meta_keywords = $translation?->meta_keywords ?? null;
        $dto->meta_description = $translation?->meta_description ?? null;

        return $dto;
    }
}
