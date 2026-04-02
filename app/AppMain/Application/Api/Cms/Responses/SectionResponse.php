<?php

namespace App\AppMain\Application\Api\Cms\Responses;

use App\AppMain\Core\BaseResponseDTO;

class SectionResponse extends BaseResponseDTO
{
    public string $id;
    public string $type;
    public bool $requires_data_source;
    public array $content = [];
    public array $data = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->type = $model->type;
        $dto->requires_data_source = (bool) $model->requires_data_source;

        $translation = $model->translations->first();
        $dto->content = $translation?->content ?? [];

        // Data will be populated by the service/repository layer if requires_data_source is true
        return $dto;
    }
}
