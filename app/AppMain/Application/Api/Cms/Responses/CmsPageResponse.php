<?php

namespace App\AppMain\Application\Api\Cms\Responses;

use App\AppMain\Core\BaseResponseDTO;
use App\AppMain\Application\Api\Cms\Responses\SectionResponse;

class CmsPageResponse extends BaseResponseDTO
{
    public SeoResponse $seo;
    public array $sections = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->seo = SeoResponse::fromModel($model);

        if ($model->relationLoaded('sections')) {
            $dto->sections = $model->sections->map(function ($section) {
                return SectionResponse::fromModel($section);
            })->toArray();
        }

        return $dto;
    }
}
